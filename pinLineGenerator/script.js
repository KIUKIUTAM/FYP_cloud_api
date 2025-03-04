
document.getElementById('kmzFile').addEventListener('change', async function (e) {
    const file = e.target.files[0];
    if (!file) return;

    try {
        const buffer = await file.arrayBuffer();

        const zip = await JSZip.loadAsync(buffer);

        const kmlFile = zip.file('wpmz/template.kml');
        if (!kmlFile) throw new Error('template.kml not found in KMZ file');

        const kmlContent = await kmlFile.async('text');

        const points = parseKmlCoordinates(kmlContent);
        const newKml = generateKmlWithArrows(points);
        // triggerDownload(newKml);
        const originalFileName = file.name.replace(/\.[^/.]+$/, "");
        triggerDownload(newKml, originalFileName);

    } catch (error) {
        console.error('Error processing KMZ:', error);
        alert('Error processing KMZ file: ' + error.message);
    }
});

// reads coordinates from {name}.kmz/wpmz/template.kml
function parseKmlCoordinates(kmlContent) {
    const parser = new DOMParser();
    const kmlDoc = parser.parseFromString(kmlContent, "text/xml");
    const points = [];

    kmlDoc.querySelectorAll('Placemark').forEach(placemark => {
        const coordsText = placemark.querySelector('coordinates').textContent.trim();
        // Split by whitespace or comma and take first two values
        const [lon, lat] = coordsText.split(/[\s,]+/).map(Number).filter(n => !isNaN(n));
        points.push({ lon, lat });
    });

    return points;
}

//  main function to generate line connecting pins
function generateLineString(points) {
    // Use only lon,lat coordinates
    const color = "ff0000ff";
    const coords = points.map(p => `${p.lon},${p.lat}`).join('\n');
    return `
    <Placemark>
        <LineString>
            <coordinates>${coords}</coordinates>
        </LineString>
        <Style><LineStyle><color>${color}</color><width>3</width></LineStyle></Style>
    </Placemark>`;
}

// // used to calculate placement for arrows, not used
// function calculateMidpoint(start, end) {
//     return {
//         lon: (start.lon + end.lon) / 2,
//         lat: (start.lat + end.lat) / 2
//     };
// }

// // used to calculate angle for arrows, not used
// function calculateBearing(start, end) {
//     return geodesic.Geodesic.WGS84.Inverse(
//         start.lat, start.lon, end.lat, end.lon
//     ).azi1; // Returns bearing in degrees
// }

// // used to generate arrows at mid point between pins, not used
// function generateArrows(points) {
//     let arrows = '';
//     for (let i = 0; i < points.length - 1; i++) {
//         const start = points[i];
//         const end = points[i + 1];
//         const midpoint = calculateMidpoint(start, end);

//         // // related to arrow generation, not used
//         // const bearing = calculateBearing(start, end);
//         // <heading>${bearing}</heading>
//         //     <Style>
//         //     <IconStyle>
//         //         <Icon><href>http://maps.google.com/mapfiles/kml/shapes/arrow.png</href></Icon>
//         //     </IconStyle>
//         // </Style>

//         arrows += `
//         <Placemark>
//             <Point>
//                 <coordinates>${midpoint.lon},${midpoint.lat},${midpoint.alt}</coordinates>
//             </Point>
//         </Placemark>`;
//     }
//     return arrows;
// }

//  master function to generating the kml file
function generateKmlWithArrows(points) {
    //  ${generateArrows(points)}
    return `<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
    <Document>
        <name>Path with Arrows</name>
        ${generateLineString(points)}
        
    </Document>
</kml>`;
}

function triggerDownload(kmlContent, originalFileName) {
    const blob = new Blob([kmlContent], { type: 'application/vnd.google-earth.kml+xml' });
    const link = document.getElementById('downloadLink');
    // link.href = URL.createObjectURL(blob);
    // link.download = 'path_with_arrows.kml';
    // link.style.display = 'inline';
    const nameWithoutExtension = originalFileName.replace(/\.[^/.]+$/, "");
    link.href = URL.createObjectURL(blob);
    link.download = `${nameWithoutExtension}_line.kml`;
    link.style.display = 'inline';
}