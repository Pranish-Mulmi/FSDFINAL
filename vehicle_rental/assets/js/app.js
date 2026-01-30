document.addEventListener('DOMContentLoaded', function () {
  const vehicle = document.getElementById('vehicle_id');
  const start = document.getElementById('start_date');
  const end = document.getElementById('end_date');
  const msg = document.getElementById('availabilityMsg');

  function check() {
    if (!vehicle || !start || !end || !msg) return;
    const v = vehicle.value;
    const s = start.value;
    const e = end.value;
    if (!v || !s || !e) {
      msg.textContent = '';
      return;
    }
    fetch(`/public/check_availability.php?vehicle_id=${encodeURIComponent(v)}&start_date=${encodeURIComponent(s)}&end_date=${encodeURIComponent(e)}`)
      .then(r => r.json())
      .then(data => {
        if (!data.ok) {
          msg.textContent = data.message || 'Error checking availability';
          msg.className = 'error';
        } else {
          msg.textContent = data.message;
          msg.className = data.available ? 'success' : 'error';
        }
      })
      .catch(() => {
        msg.textContent = 'Network error.';
        msg.className = 'error';
      });
  }

  ['change', 'input'].forEach(ev => {
    if (vehicle) vehicle.addEventListener(ev, check);
    if (start) start.addEventListener(ev, check);
    if (end) end.addEventListener(ev, check);
  });
});