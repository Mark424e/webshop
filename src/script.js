// Dropdown
document.addEventListener('click', function (event) {
  var isClickInside = document.getElementById('dropdown').contains(event.target);
  if (!isClickInside) {
      document.getElementById('dropdown-content').classList.add('hidden');
  }
});

document.getElementById('dropdown').addEventListener('click', function () {
  document.getElementById('dropdown-content').classList.toggle('hidden');
});