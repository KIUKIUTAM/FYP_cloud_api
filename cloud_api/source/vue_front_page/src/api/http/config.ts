export const CURRENT_CONFIG = {

  // license
  appId: '155772', // You need to go to the development website to apply.
  appKey: '22b0892c64036a75876bfac00d00cd6', // You need to go to the development website to apply.
  appLicense: 'QQyAn2xrL6QwTUsUuYWVrLQPUIor77xjBSEWh7zazMYhzk0yDttzmZxpEDOTPx250Nlv6oJJWtYn6OPvX7RXskwXhGo1BJkO/+M2nwcUFfD0w8SRu94c+B1L40ppJxeGlarr2nkxvzmbS88R0RHnXcruQHQW94Wu8182C8z/ixQ=', // You need to go to the development website to apply.

  // http
  baseURL: 'http://10.107.208.254:6789/', // This url must end with "/". Example: 'http://192.168.1.1:6789/'
  websocketURL: 'ws://10.107.208.254:6789/api/v1/ws', // Example: 'ws://192.168.1.1:6789/api/v1/ws'

  // livestreaming
  // RTMP  Note: This IP is the address of the streaming server. If you want to see livestream on web page, you need to convert the RTMP stream to WebRTC stream.
  rtmpURL: 'rtmp://10.107.208.254:1935/live/', // Example: 'rtmp://192.168.1.1/live/'
  // GB28181 Note:If you don't know what these parameters mean, you can go to Pilot2 and select the GB28181 page in the cloud platform. Where the parameters same as these parameters.
  gbServerIp: 'Please enter the server ip.',
  gbServerPort: 'Please enter the server port.',
  gbServerId: 'Please enter the server id.',
  gbAgentId: 'Please enter the agent id',
  gbPassword: 'Please enter the agent password',
  gbAgentPort: 'Please enter the local port.',
  gbAgentChannel: 'Please enter the channel.',
  // RTSP
  rtspUserName: 'root',
  rtspPassword: 'root',
  rtspPort: '8554',
  // Agora
  agoraAPPID: 'Please enter the agora app id.',
  agoraToken: 'Please enter the agora temporary token.',
  agoraChannel: 'Please enter the agora channel.',

  // map
  // You can apply on the AMap website.
  amapKey: '',

}
