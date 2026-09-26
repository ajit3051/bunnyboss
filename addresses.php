<?php
include_once("include/config.php");

$current_account_page = 'addresses';
$page_title = 'Saved Addresses';
include("include/user_account_header.php");

if ($is_logged_in):
    $saved_addresses = [];
    if ($user_id > 0) {
        $addr_stmt = $db->select("SELECT * FROM tbl_user_addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC", 'i', $user_id);
        if ($addr_stmt) {
            while ($a_row = $addr_stmt->fetch_assoc()) {
                $saved_addresses[] = $a_row;
            }
        }
    }
?>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <h4 class="dashboard-card-title">
                    <i class="icon-map-marker text-warning"></i> Saved Addresses
                </h4>
                <p class="text-muted small mb-0">Manage multiple delivery destinations for fast, one-click checkout.</p>
            </div>
            <button class="btn btn-primary btn-round btn-sm btn-open-add-address px-3" style="background-color: #19978c; border-color: #19978c; font-weight: bold;">
                <i class="icon-plus mr-1"></i> Add New Address
            </button>
        </div>

        <?php if (empty($saved_addresses)): ?>
            <div class="empty-state">
                <div class="empty-state-icon text-warning"><i class="icon-map-marker"></i></div>
                <h5>No Addresses Saved Yet</h5>
                <p>Save your home, office, or family delivery addresses here to skip typing during checkout!</p>
                <button class="btn btn-outline-primary btn-round btn-open-add-address px-4">
                    <i class="icon-plus mr-1"></i> Add First Delivery Address
                </button>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($saved_addresses as $addr): 
                    $is_def = (int)$addr['is_default'] === 1;
                ?>
                    <div class="col-md-6 mb-4">
                        <div class="address-card <?= $is_def ? 'default-address' : '' ?>">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="address-tag <?= $is_def ? 'tag-default' : '' ?>">
                                        <i class="icon-map-marker mr-1"></i> <?= htmlspecialchars($addr['title']) ?> <?= $is_def ? ' (Default)' : '' ?>
                                    </span>
                                    <?php if (!$is_def): ?>
                                        <button class="btn btn-link btn-sm p-0 text-primary font-weight-bold btn-set-default" data-id="<?= $addr['id'] ?>" style="font-size: 12px;">
                                            Set as Default
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <h6 class="font-weight-bold text-dark mb-1" style="font-size: 15px;">
                                    <?= htmlspecialchars($addr['first_name'] . ' ' . $addr['last_name']) ?>
                                </h6>
                                <p class="text-dark small mb-2" style="line-height: 1.5;">
                                    <?= htmlspecialchars($addr['street_address']) ?><br>
                                    <?= htmlspecialchars($addr['city']) ?><?= !empty($addr['state']) ? ', ' . htmlspecialchars($addr['state']) : '' ?> - <strong><?= htmlspecialchars($addr['postcode']) ?></strong>
                                </p>
                                <p class="text-muted small mb-3">
                                    <i class="icon-phone mr-1 text-primary"></i> +91 <?= htmlspecialchars($addr['phone']) ?>
                                </p>
                            </div>

                            <div class="border-top pt-3 d-flex justify-content-end align-items-center" style="gap: 8px;">
                                <button class="btn btn-outline-primary btn-xs btn-edit-address px-3" data-id="<?= $addr['id'] ?>" style="border-radius: 15px; font-weight: 600;">
                                    <i class="icon-edit"></i> Edit
                                </button>
                                <button class="btn btn-outline-danger btn-xs btn-delete-address px-3" data-id="<?= $addr['id'] ?>" style="border-radius: 15px; font-weight: 600;">
                                    <i class="icon-close"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

<?php 
endif;

include("include/user_account_footer.php");
?>
