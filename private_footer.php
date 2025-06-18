  <footer id="footer" class="footer light-background">
    <div class="container">
      <div class="copyright text-center">
        <p>
          © <span>Copyright</span> 
          <strong class="px-1 sitename">Restaurant Chatbot</strong> 
          <span>All Rights Reserved</span>
        </p>
      </div>
    </div>
  </footer>

  <!-- Scroll Top -->
     <style>
      #scroll-top {
        display: none !important;
      }
    </style>

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
      <i class="bi bi-arrow-up-short"></i>
    </a>
  
  <!-- Preloader -->
  <div id="preloader"></div>
  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

  <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
  <df-messenger
    intent="WELCOME"
    chat-title="RestaurantBot"
    agent-id="96718162-dc64-431b-a538-3184b5c257e9"
    language-code="en"
  ></df-messenger>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
        const userId = <?php echo isset($_SESSION['sessionID']) ? (int)$_SESSION['sessionID'] : 0; ?>;
        let dialogflowSessionId = '';
        function generateSessionId() {
            const randomString = Math.random().toString(36).substring(2, 15);
            if (userId > 0) {
                return `user_${userId}_${randomString}`;
            } else {
                return `guest_${randomString}`;
            }
        }
        dialogflowSessionId = generateSessionId();
        const messenger = document.querySelector('df-messenger');
        if (messenger) {
            messenger.setAttribute('session-id', dialogflowSessionId);
            console.log('Dialogflow session ID successfully set to:', dialogflowSessionId); 
        } else {
            console.error('Could not find the df-messenger component on the page.');
        }
    });
  </script>