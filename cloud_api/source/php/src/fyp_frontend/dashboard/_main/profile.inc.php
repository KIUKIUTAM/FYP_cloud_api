<?php
function getProfilePage() {
    require_once '../_inc/db.inc.php'; 
    $pdo = dbconnect();

    if (!isset($_SESSION['userid'])) {
        echo '<div class="alert alert-danger">Please <a href="../login/">login</a> to access this page.</div>';
        return;
    }
    $user_id = $_SESSION['userid'];

    // Initialize variables
    $success = false;
    $error = '';

    // Fetch user data
    $stmt = $pdo->prepare("SELECT username, email, realname FROM usertable WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_profile'])) {
        try {
            $username = htmlspecialchars($_POST['username']);
            $email = htmlspecialchars($_POST['email']);
            $realname = htmlspecialchars($_POST['realname']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if (!empty($password) && $password !== $confirm_password) {
                throw new Exception("Passwords do not match!");
            }

            // Build update query
            $query = "UPDATE usertable SET username = ?, email = ?, realname = ?";
            $params = [$username, $email, $realname];

            // Add password update if provided
            if (!empty($password)) {
                $query .= ", password = ?";
                $params[] = password_hash($password, PASSWORD_DEFAULT);
            }

            $query .= " WHERE user_id = ?";
            $params[] = $user_id;

            // Execute update
            $stmt = $pdo->prepare($query);
            if ($stmt->execute($params)) {
                $_SESSION['profile_updated'] = true;
                echo "<script>location.href='?page=4';</script>";
                exit();
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }?>
<main class="col-md-9 mx-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Profile</h1>
        <button class="btn btn-primary" id="editProfileBtn">
            <i class="fas fa-edit me-2"></i>Edit Profile
        </button>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" id="profileForm">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" 
                       value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" 
                       value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            
            <div class="col-12">
                <label class="form-label">Real Name</label>
                <input type="text" class="form-control" name="realname" 
                       value="<?php echo htmlspecialchars($user['realname']); ?>" disabled>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">New Password</label>
                <input type="password" class="form-control" name="password" 
                       placeholder="••••••••" disabled autocomplete="new-password">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" 
                       placeholder="••••••••" disabled autocomplete="new-password">
            </div>
            
            <div class="col-12 d-none" id="formButtons">
                <button type="submit" name="save_profile" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
                <button type="button" class="btn btn-secondary" id="cancelEdit">
                    Cancel
                </button>
            </div>
        </div>
    </form>
</main>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h3>Profile Updated Successfully!</h3>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('editProfileBtn');
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input');
    const formButtons = document.getElementById('formButtons');
    
    // Enable edit mode
    editBtn.addEventListener('click', () => {
        inputs.forEach(input => input.disabled = false);
        formButtons.classList.remove('d-none');
        editBtn.disabled = true;
    });

    // Cancel edit
    document.getElementById('cancelEdit').addEventListener('click', () => {
        inputs.forEach(input => {
            input.disabled = true;
            if (input.name === 'password' || input.name === 'confirm_password') {
                input.value = '';
            }
        });
        formButtons.classList.add('d-none');
        editBtn.disabled = false;
        form.reset();
    });

    <?php if (isset($_SESSION['profile_updated'])): ?>
        new bootstrap.Modal(document.getElementById('successModal')).show();
        <?php unset($_SESSION['profile_updated']); ?>
        setTimeout(() => window.location.reload(), 1500);
    <?php endif; ?>
});
</script>

<style>
input:disabled {
    background-color: #f8f9fa !important;
    opacity: 1 !important;
}
</style>
<?php } ?>