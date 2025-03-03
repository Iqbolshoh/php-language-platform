<?php
session_start();

include '../config.php';
$query = new Database();
$query->check_session('student');

$user = $query->select("users", '*', "id = ?", [$_SESSION['user']['id']], 'i')[0];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        !isset($_POST['csrf_token']) ||
        !isset($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        echo json_encode(['status' => 'error', 'title' => 'Invalid CSRF Token', 'message' => 'Invalid CSRF token!']);
        exit;
    }
    header('Content-Type: application/json');

    if ($_POST['action'] === 'edit') {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);

        if (empty($first_name) || empty($last_name)) {
            echo json_encode(['status' => 'error', 'title' => 'Validation Error', 'message' => 'All fields are required!']);
            exit;
        }

        $data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (!empty($_POST['password']) && isset($_POST['confirm_password'])) {
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            if (strlen($password) < 8) {
                echo json_encode(['status' => 'error', 'title' => 'Password', 'message' => 'Password must be at least 8 characters long!']);
                exit;
            }
            if ($password !== $confirm_password) {
                echo json_encode(['status' => 'error', 'title' => 'Password', 'message' => 'Passwords do not match!']);
                exit;
            }
            $data['password'] = $query->hashPassword($password);
            $query->delete('active_sessions', 'user_id = ? AND session_token <> ?', [$_SESSION['user']['id'], session_id()], 'is');
        }

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $encrypted_name = md5(bin2hex(random_bytes(32)) . '_' . date('Ymd_His') . '_' . uniqid('', true)) . '.' . pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $targetFile = "../src/images/profile_picture/";

            $filePath = $targetFile . $user['profile_picture'];
            if (file_exists($filePath) && $user['profile_picture'] != 'default.png') {
                unlink($filePath);
            }

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile . $encrypted_name)) {
                $data['profile_picture'] = $encrypted_name;
                $_SESSION['user']['profile_picture'] = $encrypted_name;
            }
        }

        $update = $query->update("users", $data, "id = ?", [$_SESSION['user']['id']], "i");

        if ($update) {
            $_SESSION['user']['first_name'] = $data['first_name'];
            $_SESSION['user']['last_name'] = $data['last_name'];
            $_SESSION['user']['updated_at'] = $data['updated_at'];

            echo json_encode(['status' => 'success', 'title' => 'Success!', 'message' => 'Profile updated successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'title' => 'Error!', 'message' => 'Failed to update profile!']);
        }
        exit;
    }
}
$query->generate_csrf_token();
?>

<?php include './header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg rounded-4 border-0">
            <div class="card-header bg-dark text-white text-center rounded-top-4">
                <h3 class="mb-0">Profile</h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="<?= SITE_PATH . "/src/images/profile_picture/" . $user['profile_picture']; ?>"
                        alt="Profile Picture" class="rounded-circle border border-3 border-dark shadow-sm" width="140"
                        height="140" style="object-fit: cover; transition: 0.3s;"
                        onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                    <h4 class="mt-3"><?= htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?></h4>
                    <p class="text-muted">@<?= htmlspecialchars($user['username']); ?></p>
                </div>

                <table class="table table-hover table-bordered rounded-3 overflow-hidden">
                    <tr>
                        <th class="bg-light">First Name</th>
                        <td><?= htmlspecialchars($user['first_name']); ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Last Name</th>
                        <td><?= htmlspecialchars($user['last_name']); ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Email</th>
                        <td><?= htmlspecialchars($user['email']); ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Username</th>
                        <td><?= htmlspecialchars($user['username']); ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Role</th>
                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($user['role']); ?></span></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Created At</th>
                        <td><?= date('H:i:s d-m-Y', strtotime($user['created_at'])); ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Updated At</th>
                        <td><?= date('H:i:s d-m-Y', strtotime($user['updated_at'])); ?></td>
                    </tr>
                </table>

                <div class="d-flex justify-content-center mt-4">
                    <button type="button" class="btn btn-warning px-4 py-2 fw-bold shadow-lg rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#editModal"
                        style="font-size: 16px; transition: 0.3s ease-in-out;"
                        onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data" id="editProfileForm">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-dark text-white text-center rounded-top-4">
                    <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"
                        style="background: transparent; border: none; font-size: 24px; font-weight: bold; color: white; cursor: pointer; line-height: 1;"
                        onmouseover="this.style.color='#ff4d4d'; this.style.transform='scale(1.2)'; this.style.transition='0.2s';"
                        onmouseout="this.style.color='white'; this.style.transform='scale(1)';">
                        ×
                    </button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control"
                            value="<?php echo htmlspecialchars($user['first_name']); ?>" maxlength="30" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control"
                            value="<?php echo htmlspecialchars($user['last_name']); ?>" maxlength="30" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>"
                            maxlength="100" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control"
                            value="<?php echo htmlspecialchars($user['username']); ?>" maxlength="30" disabled>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control" maxlength="255">
                            <button type="button" id="toggle-password" class="btn btn-outline-secondary">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small id="password-message" class="text-danger"></small>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                                maxlength="255">
                            <button type="button" id="toggle-confirm-password" class="btn btn-outline-secondary">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small id="confirm-password-message" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="form-label fw-bold">Upload Image</label>
                        <div class="input-group">
                            <input type="file" name="profile_picture" id="profile_picture" accept="image/*"
                                style="display: none;">
                            <label for="profile_picture" style="background-color: white;
                                color: #495057; 
                                border: 2px solid #495057; 
                                border-radius: 5px; 
                                padding: 7px; 
                                cursor: pointer; 
                                transition: 0.3s; 
                                width: 100%; 
                                text-align: center; 
                                font-weight: bold; 
                                display: inline-block;">
                                📂 Upload Image
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="hidden" name="action" value="edit">
                    </div>
                    <div class="mb-3">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" id="submit" class="btn btn-primary w-100">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirm_password');
        const togglePassword = document.getElementById('toggle-password');
        const toggleConfirmPassword = document.getElementById('toggle-confirm-password');
        const passwordMessage = document.getElementById('password-message');
        const confirmPasswordMessage = document.getElementById('confirm-password-message');
        const submitBtn = document.getElementById('submit');

        function toggleVisibility(field, icon) {
            field.type = field.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }

        togglePassword.addEventListener('click', function () {
            toggleVisibility(passwordField, this.querySelector('i'));
        });

        toggleConfirmPassword.addEventListener('click', function () {
            toggleVisibility(confirmPasswordField, this.querySelector('i'));
        });

        function validatePasswords() {
            const passwordValid = passwordField.value.length >= 8;
            const passwordsMatch = passwordField.value === confirmPasswordField.value && passwordValid;

            passwordMessage.textContent = passwordValid ? '' : 'Password must be at least 8 characters long!';
            confirmPasswordMessage.textContent = passwordsMatch ? '' : 'Passwords do not match!';
        }

        passwordField.addEventListener('input', validatePasswords);
        confirmPasswordField.addEventListener('input', validatePasswords);

        document.getElementById('editProfileForm').addEventListener('submit', function (event) {
            event.preventDefault();

            let formData = new FormData(this);

            fetch('', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({ icon: 'success', title: data.title, text: data.message, timer: 1500, showConfirmButton: false }).then(() => { window.location.reload(); });
                    } else {
                        Swal.fire({ icon: 'error', title: data.title, text: data.message, showConfirmButton: true });
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });
</script>

<?php include './footer.php'; ?>