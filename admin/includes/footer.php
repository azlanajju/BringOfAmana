      </div><!-- .main-content -->
    </main>
  </div>
  <script>
    (function() {
      var body = document.body;
      var btn = document.getElementById('hamburgerBtn');
      var overlay = document.getElementById('sidebarOverlay');
      var sidebar = document.getElementById('sidebar');

      function openNav() {
        body.classList.add('mobile-nav-open');
        if (btn) btn.setAttribute('aria-expanded', 'true');
        if (overlay) overlay.setAttribute('aria-hidden', 'false');
      }
      function closeNav() {
        body.classList.remove('mobile-nav-open');
        if (btn) btn.setAttribute('aria-expanded', 'false');
        if (overlay) overlay.setAttribute('aria-hidden', 'true');
      }

      if (btn) btn.addEventListener('click', function() {
        body.classList.contains('mobile-nav-open') ? closeNav() : openNav();
      });
      if (overlay) overlay.addEventListener('click', closeNav);

      var navLinks = document.querySelectorAll('.sidebar-nav a');
      for (var i = 0; i < navLinks.length; i++) {
        navLinks[i].addEventListener('click', closeNav);
      }

      window.addEventListener('resize', function() {
        if (window.innerWidth > 768) closeNav();
      });
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && body.classList.contains('mobile-nav-open')) closeNav();
      });
    })();
  </script>
</body>
</html>
