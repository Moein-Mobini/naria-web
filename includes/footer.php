  <footer>
    <div class="container footer">
      <div><strong class="brand">NARIA</strong><br><small>Tobacco & Accessories</small></div>
      <div>© <?= date('Y') ?> NARIA — All rights reserved.</div>
    </div>
  </footer>

  <div class="toast" id="toast"></div>

  <script>
    (function(){
      var saved = localStorage.getItem('nariaTheme');
      if(saved === 'light' || saved === 'dark') document.documentElement.setAttribute('data-theme', saved);
      var btn = document.getElementById('themeToggle');
      if(btn){ btn.addEventListener('click', function(){
        var current = document.documentElement.getAttribute('data-theme');
        var systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        var next = current ? (current === 'dark' ? 'light' : 'dark') : (systemDark ? 'light' : 'dark');
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('nariaTheme', next);
      }); }
    })();

    function enterSite(){
      localStorage.setItem('nariaAge','ok');
      document.getElementById('ageGate').style.display='none';
    }
    function leaveSite(){ window.location.href='https://www.google.com'; }
    if(localStorage.getItem('nariaAge')==='ok'){
      var g = document.getElementById('ageGate');
      if(g) g.style.display='none';
    }

    function showToast(msg, duration){
      duration = duration || 2800;
      var t = document.getElementById('toast');
      t.textContent = msg;
      t.classList.add('show');
      setTimeout(function(){ t.classList.remove('show'); }, duration);
    }

    function addToCart(id, name, price){
      fetch('api/cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'add', id: id, name: name, price: price, qty: 1})
      })
      .then(r => r.json())
      .then(data => {
        if(data.ok){
          showToast('✓ به سبد خرید اضافه شد');
          var el = document.getElementById('cartCount');
          if(el){
            el.style.display = 'flex';
            el.textContent = data.count;
          }
        } else {
          showToast(data.error || 'خطا');
        }
      })
      .catch(() => showToast('خطا در ارتباط با سرور'));
    }

    function updateCartQty(id, qty){
      fetch('api/cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'update', id: id, qty: parseInt(qty)})
      })
      .then(r => r.json())
      .then(data => {
        if(data.ok) location.reload();
      });
    }

    function removeFromCart(id){
      fetch('api/cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'remove', id: id})
      })
      .then(r => r.json())
      .then(data => {
        if(data.ok) location.reload();
      });
    }
  </script>
</body>
</html>
