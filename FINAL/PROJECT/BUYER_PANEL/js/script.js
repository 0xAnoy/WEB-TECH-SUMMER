document.addEventListener('DOMContentLoaded', function() {
  console.log('script.js loaded');

  // Quantity auto-submit for cart updates
  document.querySelectorAll('form').forEach(function(form) {
    if (form.querySelector('input[name="update_id"]') && form.querySelector('input[name="quantity"]')) {
      const qty = form.querySelector('input[name="quantity"]');
      let timer = null;
      qty.addEventListener('input', function() {
        if (timer) clearTimeout(timer);
        timer = setTimeout(function() { form.submit(); }, 500);
      });
    }
  });

  // Toast system
  const toastContainer = document.getElementById('toastContainer');
  function showToast(msg, cls) {
    if (!toastContainer) return;
    const t = document.createElement('div');
    t.className = 'toast toast-success';
    if (cls) t.className += ' ' + cls;
    t.textContent = msg;
    toastContainer.appendChild(t);
    void t.offsetWidth;
    t.classList.add('toast-show');
    setTimeout(()=>{ t.classList.remove('toast-show'); setTimeout(()=> t.remove(), 400); }, 3000);
  }

  document.querySelectorAll('[data-toast]').forEach(function(el){
    const msg = el.getAttribute('data-toast');
    if (msg) showToast(msg);
  });

  // Disclaimer modal logic
  const disclaimerModal = document.getElementById('disclaimerModal');
  if (disclaimerModal) {
    const accept = document.getElementById('disclaimerAccept');
    const closeBtn = document.getElementById('disclaimerClose');
    const addBtn = document.getElementById('addToCartBtn');
    const form = document.getElementById('addToCartForm');
    if (accept) accept.addEventListener('click', function(){
      disclaimerModal.style.display = 'none';
      if (addBtn) addBtn.removeAttribute('disabled');
    });
    if (closeBtn) closeBtn.addEventListener('click', function(){
      disclaimerModal.style.display = 'none';
    });
    if (form && addBtn) {
      form.addEventListener('submit', function(e){
        if (addBtn.disabled) {
          e.preventDefault();
          alert('Please read and accept the disclaimer before adding to cart.');
        }
      });
    }
  }
});
