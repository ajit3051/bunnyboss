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
      <!-- Sign in / Register Modal -->
      <div class="modal fade" id="signin-modal" tabindex="-1" role="dialog" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <div class="modal-body">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true"><i class="icon-close"></i></span>
                  </button>
                  <div class="form-box">
                     <div class="form-tab">
                        <ul class="nav nav-pills nav-fill" role="tablist">
                           <li class="nav-item">
                              <a class="nav-link active" id="mobile-otp-tab" data-toggle="tab" href="#mobile-otp-pane" role="tab" aria-controls="mobile-otp-pane" aria-selected="true"><i class="icon-phone"></i> Mobile OTP</a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" id="signin-tab" data-toggle="tab" href="#signin" role="tab" aria-controls="signin" aria-selected="false">Sign In</a>
                           </li>
                        </ul>
                        <div class="tab-content" id="tab-content-5">
                           <!-- Mobile OTP Pane -->
                           <div class="tab-pane fade show active" id="mobile-otp-pane" role="tabpanel" aria-labelledby="mobile-otp-tab">
                              <!-- Step 1: Send OTP -->
                              <form id="form-send-otp">
                                 <div class="form-group mb-2">
                                    <label for="otp-mobile">Mobile Number *</label>
                                    <div class="input-group">
                                       <div class="input-group-prepend">
                                          <span class="input-group-text font-weight-bold">+91</span>
                                       </div>
                                       <input type="tel" class="form-control" id="otp-mobile" name="mobile" placeholder="Enter 10-digit mobile number" maxlength="10" required pattern="[6-9][0-9]{9}">
                                    </div>
                                    <small class="form-text text-muted">We will send a 6-digit OTP to verify your mobile number.</small>
                                 </div>
                                 <div id="otp-msg-step1" class="mb-2"></div>
                                 <div class="form-footer mt-3">
                                    <button type="submit" id="btn-send-otp" class="btn btn-outline-primary-2 btn-block">
                                       <span>GET OTP</span>
                                       <i class="icon-long-arrow-right"></i>
                                    </button>
                                 </div>
                              </form>

                              <!-- Step 2: Verify OTP -->
                              <form id="form-verify-otp" style="display: none;">
                                 <div class="text-center mb-3">
                                    <p class="mb-1">OTP sent to <strong>+91-<span id="display-otp-mobile"></span></strong></p>
                                    <button type="button" id="btn-change-mobile" class="btn btn-link btn-sm p-0 text-primary"><i class="icon-edit"></i> Change Mobile Number</button>
                                 </div>
                                 <div id="debug-otp-alert" class="alert alert-info py-2 px-3 text-center mb-3" style="display: none;">
                                    <strong>Test OTP:</strong> <span id="debug-otp-code" style="font-size: 18px; font-weight: bold; letter-spacing: 3px;"></span>
                                 </div>
                                 <div class="form-group mb-2">
                                    <label for="otp-code">Enter 6-Digit OTP *</label>
                                    <input type="text" class="form-control text-center font-weight-bold" id="otp-code" name="otp" placeholder="• • • • • •" maxlength="6" style="font-size: 22px; letter-spacing: 6px;" required autocomplete="off">
                                 </div>
                                 <div id="otp-msg-step2" class="mb-2"></div>
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
                           </div>

                           <!-- Email Signin Pane -->
                           <div class="tab-pane fade" id="signin" role="tabpanel" aria-labelledby="signin-tab">
                              <form action="#">
                                 <div class="form-group">
                                    <label for="singin-email">Username or email address *</label>
                                    <input type="text" class="form-control" id="singin-email" name="singin-email" required>
                                 </div>
                                 <!-- End .form-group -->
                                 <div class="form-group">
                                    <label for="singin-password">Password *</label>
                                    <input type="password" class="form-control" id="singin-password" name="singin-password" required>
                                 </div>
                                 <!-- End .form-group -->
                                 <div class="form-footer">
                                    <button type="submit" class="btn btn-outline-primary-2">
                                    <span>LOG IN</span>
                                    <i class="icon-long-arrow-right"></i>
                                    </button>
                                    <div class="custom-control custom-checkbox">
                                       <input type="checkbox" class="custom-control-input" id="signin-remember">
                                       <label class="custom-control-label" for="signin-remember">Remember Me</label>
                                    </div>
                                    <!-- End .custom-checkbox -->
                                    <a href="#" class="forgot-link">Forgot Your Password?</a>
                                 </div>
                                 <!-- End .form-footer -->
                              </form>
                           </div>
                           <!-- .End .tab-pane -->
                        </div>
                        <!-- End .tab-content -->
                     </div>
                     <!-- End .form-tab -->
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

          // Step 1: Send OTP
          $('#form-send-otp').on('submit', function(e) {
              e.preventDefault();
              var mobile = $('#otp-mobile').val().trim();
              var $btn = $('#btn-send-otp');
              var $msg = $('#otp-msg-step1');

              if (!/^[6-9]\d{9}$/.test(mobile)) {
                  $msg.html('<div class="alert alert-danger py-2">Please enter a valid 10-digit mobile number.</div>');
                  return;
              }

              $btn.prop('disabled', true).find('span').text('SENDING...');
              $msg.html('');

              $.ajax({
                  url: '<?= _BASEURL ?>include/auth_api.php',
                  type: 'POST',
                  data: { action: 'send_otp', mobile: mobile },
                  dataType: 'json',
                  success: function(res) {
                      $btn.prop('disabled', false).find('span').text('GET OTP');
                      if (res.success) {
                          $('#display-otp-mobile').text(mobile);
                          if (res.debug_otp) {
                              $('#debug-otp-code').text(res.debug_otp);
                              $('#debug-otp-alert').show();
                          } else {
                              $('#debug-otp-alert').hide();
                          }
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