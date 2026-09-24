<?php if (empty($hide_footer)): ?>
<footer class="footer footer-2">
         <?php include('include/footer.php');?>  
      </footer>
      <!-- End .footer -->
<?php endif; ?>
      </div><!-- End .page-wrapper -->
      <button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
      <!-- Mobile Menu -->
      <div class="mobile-menu-overlay"></div>
      <!-- End .mobil-menu-overlay -->
      <div class="mobile-menu-container">
         <div class="mobile-menu-wrapper">
            <span class="mobile-menu-close"><i class="icon-close"></i></span>
            <form action="<?= _BASEURL ?>product-list.php" method="get" class="mobile-search">
               <label for="mobile-search" class="sr-only">Search</label>
               <input type="search" class="form-control" name="q" id="mobile-search" placeholder="Search products..." required>
               <button class="btn btn-primary" type="submit"><i class="icon-search"></i></button>
            </form>
            
            <nav class="mobile-nav">
               <ul class="mobile-menu">
                  <li class="active">
                     <a href="<?= _BASEURL ?>index.php">Home</a>
                  </li>
                  <li>
                     <a href="#">Categories</a>
                     <ul>
                        <?php
                        if (function_exists('getGroupList')) {
                           $mobile_drawer_groups = getGroupList();
                           while ($group = $mobile_drawer_groups->fetch_assoc()) {
                              echo '<li><a href="' . _BASEURL . 'product-list.php?category=' . urlencode($group['group_name']) . '">' . htmlspecialchars($group['group_name']) . '</a></li>';
                           }
                        }
                        ?>
                     </ul>
                  </li>
                  <li>
                     <a href="<?= _BASEURL ?>cart.php">Cart</a>
                  </li>
                  <li>
                     <a href="<?= _BASEURL ?>checkout.php">Checkout</a>
                  </li>
                  <li>
                     <a href="<?= _BASEURL ?>contact.php">Contact Us</a>
                  </li>
                  <li>
                     <a href="<?= _BASEURL ?>about.php">About Us</a>
                  </li>
                  <?php if (!empty($_SESSION['user_id'])): ?>
                     <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'staff'], true)): ?>
                        <li>
                           <a href="<?= _ADMIN_URL ?>index.php" class="text-primary font-weight-bold"><i class="icon-dashboard"></i> Admin Panel</a>
                        </li>
                     <?php endif; ?>
                     <li>
                        <a href="<?= _BASEURL ?>logout.php" class="text-danger"><i class="icon-long-arrow-right"></i> Logout (<?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>)</a>
                     </li>
                  <?php else: ?>
                     <li>
                        <a href="#signin-modal" data-toggle="modal"><i class="icon-user"></i> Sign In / Register</a>
                     </li>
                  <?php endif; ?>
               </ul>
            </nav>
            <!-- End .mobile-nav -->

            <div class="social-icons">
               <a href="#" class="social-icon" target="_blank" title="Facebook"><i class="icon-facebook-f"></i></a>
               <a href="#" class="social-icon" target="_blank" title="Twitter"><i class="icon-twitter"></i></a>
               <a href="#" class="social-icon" target="_blank" title="Instagram"><i class="icon-instagram"></i></a>
               <a href="#" class="social-icon" target="_blank" title="Youtube"><i class="icon-youtube"></i></a>
            </div>
            <!-- End .social-icons -->
         </div>
         <!-- End .mobile-menu-wrapper -->
      </div>
      <!-- End .mobile-menu-container -->
      <style>
         /* Enforce highest stacking order for signin and location modals */
         .modal-backdrop {
            z-index: 105000 !important;
         }
         .modal {
            z-index: 105005 !important;
         }
         #signin-modal,
         #location-modal {
            z-index: 105005 !important;
         }
         #signin-modal .modal-dialog,
         #location-modal .modal-dialog {
            z-index: 105010 !important;
         }
         #signin-modal .modal-content {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
         }
         body.modal-open .offer-marquee,
         body.modal-open .offer-bar,
         body.modal-open .desktop-header-wrap,
         body.modal-open .mobile-header,
         body.modal-open header.header {
            z-index: 1 !important;
         }
         #otp-msg-step1 .alert,
         #otp-msg-step2 .alert {
            font-size: 13px;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 10px;
            font-weight: 500;
         }
      </style>
      <!-- Sign in / Register Modal -->
      <div class="modal fade" id="signin-modal" tabindex="-1" role="dialog" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <div class="modal-body">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true"><i class="icon-close"></i></span>
                  </button>
                  <div class="form-box">
                     <?php $is_sms_active = defined('_ENABLE_SMS_') && (bool)_ENABLE_SMS_; ?>
                     <div class="text-center mb-3">
                        <h4 class="font-weight-bold mb-1" style="font-size: 20px; color: #1e293b;"><i class="icon-phone mr-1"></i> Sign In with Mobile<?= $is_sms_active ? ' OTP' : '' ?></h4>
                        <p class="text-muted small mb-0"><?= $is_sms_active ? 'Fast &amp; secure passwordless verification' : 'Enter your mobile number to sign in instantly' ?></p>
                     </div>
                     <div class="tab-content" id="tab-content-5">
                        <!-- Mobile Login Pane -->
                        <div class="tab-pane fade show active" id="mobile-otp-pane" role="tabpanel">
                           <!-- Step 1: Mobile Number Input -->
                           <form id="form-send-otp" novalidate>
                              <div id="otp-msg-step1" class="mb-2"></div>
                              <div class="form-group mb-2">
                                 <label for="otp-mobile">Mobile Number *</label>
                                 <div class="input-group">
                                    <div class="input-group-prepend">
                                       <span class="input-group-text font-weight-bold">+91</span>
                                    </div>
                                    <input type="tel" class="form-control" id="otp-mobile" name="mobile" placeholder="Enter 10-digit mobile number" maxlength="16" autocomplete="tel" inputmode="numeric" required>
                                 </div>
                                 <small class="form-text text-muted"><?= $is_sms_active ? 'We will send a 6-digit OTP to verify your mobile number.' : 'Enter your 10-digit mobile number to log in.' ?></small>
                              </div>
                              <div class="form-footer mt-3">
                                 <button type="submit" id="btn-send-otp" class="btn btn-outline-primary-2 btn-block">
                                    <span><?= $is_sms_active ? 'GET OTP' : 'LOG IN' ?></span>
                                    <i class="icon-long-arrow-right"></i>
                                 </button>
                              </div>
                           </form>

                           <?php if ($is_sms_active): ?>
                           <!-- Step 2: Verify OTP (Only when SMS is enabled) -->
                           <form id="form-verify-otp" style="display: none;">
                              <div class="text-center mb-3">
                                 <p class="mb-1">OTP sent to <strong>+91-<span id="display-otp-mobile"></span></strong></p>
                                 <button type="button" id="btn-change-mobile" class="btn btn-link btn-sm p-0 text-primary"><i class="icon-edit"></i> Change Mobile Number</button>
                              </div>
                              <div id="otp-msg-step2" class="mb-2"></div>
                              <div class="form-group mb-2">
                                 <label for="otp-code">Enter 6-Digit OTP *</label>
                                 <input type="text" class="form-control text-center font-weight-bold" id="otp-code" name="otp" placeholder="• • • • • •" maxlength="6" style="font-size: 22px; letter-spacing: 6px;" required autocomplete="off">
                              </div>
                              <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                                 <span id="timer-text" class="text-muted small">Resend in <strong id="otp-countdown">30</strong>s</span>
                                 <button type="button" id="btn-resend-otp" class="btn btn-link btn-sm p-0" style="display: none;">Resend OTP</button>
                              </div>
                              <div class="form-footer mt-2">
                                 <button type="submit" id="btn-verify-otp" class="btn btn-primary btn-block">
                                    <span>VERIFY & LOGIN</span>
                                    <i class="icon-check"></i>
                                 </button>
                              </div>
                           </form>
                           <?php endif; ?>
                        </div>
                     </div>
                  </div>
                  <!-- End .form-box -->
               </div>
               <!-- End .modal-body -->
            </div>
            <!-- End .modal-content -->
         </div>
         <!-- End .modal-dialog -->
      </div>
      <!-- End .modal -->
      <?php include('include/location_modal.php'); ?>
      <script>
      document.addEventListener('DOMContentLoaded', function() {
          if (typeof jQuery === 'undefined') return;
          var $ = jQuery;
          var otpTimerInterval = null;

          function startOtpTimer(seconds) {
              clearInterval(otpTimerInterval);
              var count = seconds;
              $('#timer-text').show();
              $('#otp-countdown').text(count);
              $('#btn-resend-otp').hide();

              otpTimerInterval = setInterval(function() {
                  count--;
                  $('#otp-countdown').text(count);
                  if (count <= 0) {
                      clearInterval(otpTimerInterval);
                      $('#timer-text').hide();
                      $('#btn-resend-otp').show();
                  }
              }, 1000);
          }

          function cleanMobile(raw) {
              if (!raw) return '';
              var val = String(raw).replace(/\D/g, '');
              if (val.indexOf('0091') === 0 && val.length === 14) val = val.substring(4);
              else if (val.length === 12 && val.indexOf('91') === 0) val = val.substring(2);
              else if (val.length === 11 && val.indexOf('0') === 0) val = val.substring(1);
              else if (val.length > 0 && val.charAt(0) === '0') val = val.replace(/^0+/, '');
              return val.slice(0, 10);
          }

          function checkMobile(num) {
              if (!num) return { valid: false, message: 'Please enter your 10-digit mobile number.' };
              var c = cleanMobile(num);
              if (c.length < 10) return { valid: false, message: 'Please enter complete 10-digit mobile number (' + c.length + '/10 entered).' };
              if (!/^[6-9]/.test(c)) return { valid: false, message: 'Mobile number must start with 6, 7, 8, or 9.' };
              if (!/^[6-9]\d{9}$/.test(c)) return { valid: false, message: 'Please enter a valid 10-digit mobile number.' };
              if (/^(\d)\1{9}$/.test(c)) return { valid: false, message: 'Please enter a genuine, active mobile number.' };
              return { valid: true, clean: c };
          }

          // Real-time mobile input formatting and validation
          $('#otp-mobile').on('input', function() {
              var raw = $(this).val();
              var cleaned = cleanMobile(raw);
              if (raw !== cleaned) {
                  $(this).val(cleaned);
              }
              if (cleaned.length === 10) {
                  var res = checkMobile(cleaned);
                  if (res.valid) {
                      $(this).removeClass('is-invalid').addClass('is-valid');
                      $('#otp-msg-step1').html('');
                  } else {
                      $(this).removeClass('is-valid').addClass('is-invalid');
                      $('#otp-msg-step1').html('<div class="alert alert-danger py-2">' + res.message + '</div>');
                  }
              } else {
                  $(this).removeClass('is-valid is-invalid');
                  $('#otp-msg-step1').html('');
              }
          });

          $('#otp-mobile').on('paste', function() {
              var $this = $(this);
              setTimeout(function() {
                  $this.val(cleanMobile($this.val())).trigger('input');
              }, 10);
          });

          $('#otp-mobile').on('blur', function() {
              var val = cleanMobile($(this).val());
              if (val.length > 0 && val.length < 10) {
                  $(this).removeClass('is-valid').addClass('is-invalid');
                  $('#otp-msg-step1').html('<div class="alert alert-danger py-2">Please enter complete 10-digit mobile number (' + val.length + '/10 entered).</div>');
              }
          });

          var isSmsActive = <?= json_encode($is_sms_active) ?>;

          // Step 1: Send OTP or Direct Mobile Login
          $('#form-send-otp').on('submit', function(e) {
              e.preventDefault();
              var $input = $('#otp-mobile');
              var mobile = cleanMobile($input.val());
              $input.val(mobile);
              var $btn = $('#btn-send-otp');
              var $msg = $('#otp-msg-step1');

              var check = checkMobile(mobile);
              if (!check.valid) {
                  $input.removeClass('is-valid').addClass('is-invalid').focus();
                  $msg.html('<div class="alert alert-danger py-2">' + check.message + '</div>');
                  return;
              }

              $input.removeClass('is-invalid').addClass('is-valid');
              $btn.prop('disabled', true).find('span').text(isSmsActive ? 'SENDING...' : 'LOGGING IN...');
              $msg.html('');

              if (isSmsActive) {
                  $.ajax({
                      url: '<?= _BASEURL ?>include/auth_api.php',
                      type: 'POST',
                      data: { action: 'send_otp', mobile: mobile },
                      dataType: 'json',
                      success: function(res) {
                          $btn.prop('disabled', false).find('span').text('GET OTP');
                          if (res.success) {
                              $('#display-otp-mobile').text(mobile);
                              $('#form-send-otp').hide();
                              $('#form-verify-otp').show();
                              $('#otp-code').val('').focus();
                              startOtpTimer(30);
                          } else {
                              $msg.html('<div class="alert alert-danger py-2">' + res.message + '</div>');
                          }
                      },
                      error: function() {
                          $btn.prop('disabled', false).find('span').text('GET OTP');
                          $msg.html('<div class="alert alert-danger py-2">Failed to send OTP. Please try again.</div>');
                      }
                  });
              } else {
                  // Direct mobile login without OTP when SMS is disabled
                  $.ajax({
                      url: '<?= _BASEURL ?>include/auth_api.php',
                      type: 'POST',
                      data: { action: 'mobile_direct_login', mobile: mobile },
                      dataType: 'json',
                      success: function(res) {
                          if (res.success) {
                              $btn.find('span').text('SUCCESS!');
                              $msg.html('<div class="alert alert-success py-2">' + res.message + ' Redirecting...</div>');
                              setTimeout(function() {
                                  window.location.href = res.redirect_url || window.location.href;
                              }, 800);
                          } else {
                              $btn.prop('disabled', false).find('span').text('LOG IN');
                              $msg.html('<div class="alert alert-danger py-2">' + res.message + '</div>');
                          }
                      },
                      error: function() {
                          $btn.prop('disabled', false).find('span').text('LOG IN');
                          $msg.html('<div class="alert alert-danger py-2">Login failed. Please try again.</div>');
                      }
                  });
              }
          });

          // Step 2: Verify OTP
          $('#form-verify-otp').on('submit', function(e) {
              e.preventDefault();
              var mobile = $('#otp-mobile').val().trim();
              var otp = $('#otp-code').val().trim();
              var $btn = $('#btn-verify-otp');
              var $msg = $('#otp-msg-step2');

              if (otp.length !== 6) {
                  $msg.html('<div class="alert alert-danger py-2">Please enter a valid 6-digit OTP.</div>');
                  return;
              }

              $btn.prop('disabled', true).find('span').text('VERIFYING...');
              $msg.html('');

              $.ajax({
                  url: '<?= _BASEURL ?>include/auth_api.php',
                  type: 'POST',
                  data: { action: 'verify_otp', mobile: mobile, otp: otp },
                  dataType: 'json',
                  success: function(res) {
                      if (res.success) {
                          $msg.html('<div class="alert alert-success py-2">' + res.message + ' Redirecting...</div>');
                          setTimeout(function() {
                              window.location.href = res.redirect_url;
                          }, 1000);
                      } else {
                          $btn.prop('disabled', false).find('span').text('VERIFY & LOGIN');
                          $msg.html('<div class="alert alert-danger py-2">' + res.message + '</div>');
                      }
                  },
                  error: function() {
                      $btn.prop('disabled', false).find('span').text('VERIFY & LOGIN');
                      $msg.html('<div class="alert alert-danger py-2">Verification failed. Please try again.</div>');
                  }
              });
          });

          // Change Mobile
          $('#btn-change-mobile').on('click', function() {
              $('#form-verify-otp').hide();
              $('#form-send-otp').show();
              $('#otp-mobile').removeClass('is-valid is-invalid').focus();
              $('#otp-msg-step1').html('');
              $('#otp-msg-step2').html('');
              clearInterval(otpTimerInterval);
          });

          // Resend OTP
          $('#btn-resend-otp').on('click', function() {
              $('#form-send-otp').trigger('submit');
          });
      });
      </script>
      <?php // include('include/popup_dashboard.php'); ?> 
      <!-- Plugins JS File -->
      <script src="<?= _BASEURL ?>assets/js/page/custom.js?v=3.1"></script>
      <script src="<?= _BASEURL ?>assets/js/bootstrap.bundle.min.js"></script>
      <script src="<?= _BASEURL ?>assets/js/jquery.hoverIntent.min.js"></script>
      <script src="<?= _BASEURL ?>assets/js/jquery.waypoints.min.js"></script>
      <script src="<?= _BASEURL ?>assets/js/superfish.min.js"></script>
      <script src="<?= _BASEURL ?>assets/js/bootstrap-input-spinner.js"></script>
      <script src="<?= _BASEURL ?>assets/js/owl.carousel.min.js"></script>
      <script src="<?= _BASEURL ?>assets/js/jquery.plugin.min.js"></script>
      <script src="<?= _BASEURL ?>assets/js/jquery.magnific-popup.min.js"></script>
      <script src="<?= _BASEURL ?>assets/js/jquery.countdown.min.js"></script>
      <script src="<?= _BASEURL ?>assets/js/wNumb.js"></script>
      <script src="<?= _BASEURL ?>assets/js/nouislider.min.js"></script>
      <script src="<?= _BASEURL ?>assets/js/jquery.elevateZoom.min.js"></script>
      <!-- Main JS File -->
      <script src="<?= _BASEURL ?>assets/js/main.js"></script>
      <script src="<?= _BASEURL ?>assets/js/demos/demo-7.js"></script>
      <script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9b4746c4c81afeb7',t:'MTc2NjgyMjM0NA=='};var a=document.createElement('script');a.src='../../cdn-cgi/challenge-platform/h/g/scripts/jsd/d39f91d70ce1/maind41d.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script><script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"version":"2024.11.0","token":"ecd4920e43e14654b78e65dbf8311922","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}' crossorigin="anonymous"></script>
   </body>
   <!-- Mirrored from portotheme.com/html/molla/index-7.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 27 Dec 2025 07:58:19 GMT -->
</html>