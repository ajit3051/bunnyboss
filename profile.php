<?php
include_once("include/config.php");

$current_account_page = 'profile';
$page_title = 'Basic Details';
include("include/user_account_header.php");

if ($is_logged_in):
?>

    <div class="row">
        <!-- EDIT PROFILE FORM -->
        <div class="col-lg-8 mb-4">
            <div class="dashboard-card h-100">
                <div class="dashboard-card-header">
                    <div>
                        <h4 class="dashboard-card-title">
                            <i class="icon-user text-info"></i> Basic Account Details
                        </h4>
                        <p class="text-muted small mb-0">Update your name and communication email address.</p>
                    </div>
                </div>

                <div id="profile-alert" class="alert d-none" role="alert"></div>

                <form id="profile-form">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">Registered Mobile Number</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light" style="border-radius: 8px 0 0 8px;">+91</span>
                            </div>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['mobile'] ?? '') ?>" readonly>
                            <div class="input-group-append">
                                <span class="input-group-text bg-light text-success font-weight-bold" style="border-radius: 0 8px 8px 0; font-size: 12px;">
                                    <i class="icon-check mr-1"></i> Verified
                                </span>
                            </div>
                        </div>
                        <small class="form-text text-muted">Your registered mobile number is verified with secure OTP and used for order confirmations.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="profile_name" class="font-weight-bold small text-dark">Full Name *</label>
                        <input type="text" class="form-control" id="profile_name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" placeholder="Enter your full name" required style="border-radius: 8px;">
                    </div>

                    <div class="form-group mb-4">
                        <label for="profile_email" class="font-weight-bold small text-dark">Email Address</label>
                        <input type="email" class="form-control" id="profile_email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="name@example.com" style="border-radius: 8px;">
                        <small class="form-text text-muted">We will send digital invoices and courier delivery updates to this email address.</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-round px-4" id="btn-save-profile" style="background-color: #19978c; border-color: #19978c; font-weight: bold;">
                        <i class="icon-check mr-1"></i> SAVE CHANGES
                    </button>
                </form>
            </div>
        </div>

        <!-- ACCOUNT SECURITY & SUMMARY -->
        <div class="col-lg-4 mb-4">
            <div class="dashboard-card h-100">
                <div class="dashboard-card-header">
                    <h5 class="dashboard-card-title" style="font-size: 16px;">
                        <i class="icon-info-circle text-primary"></i> Account Summary
                    </h5>
                </div>

                <div class="account-summary-item mb-3 pb-3 border-bottom">
                    <span class="text-muted small d-block">Account ID</span>
                    <strong class="text-dark">#BB-USR-<?= str_pad($user_id, 5, '0', STR_PAD_LEFT) ?></strong>
                </div>

                <div class="account-summary-item mb-3 pb-3 border-bottom">
                    <span class="text-muted small d-block">Member Since</span>
                    <strong class="text-dark"><?= !empty($user['created_at']) ? date('d F Y', strtotime($user['created_at'])) : 'Recent Member' ?></strong>
                </div>

                <div class="account-summary-item mb-3 pb-3 border-bottom">
                    <span class="text-muted small d-block">Total Orders</span>
                    <strong class="text-dark"><?= $total_orders_count ?> order(s) placed</strong>
                </div>

                <div class="p-3 bg-light rounded text-center" style="border-radius: 10px;">
                    <i class="icon-lock text-success mb-2" style="font-size: 28px; display: inline-block;"></i>
                    <h6 class="font-weight-bold text-dark small mb-1">Passwordless OTP Security</h6>
                    <p class="text-muted small mb-0" style="font-size: 11px;">
                        Your account is secured via one-time SMS verification sent directly to your phone. No passwords to remember or lose.
                    </p>
                </div>
            </div>
        </div>
    </div>

<?php 
endif;

include("include/user_account_footer.php");
?>
