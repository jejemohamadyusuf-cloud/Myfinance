import './bootstrap';

window.handleLogout = function (element, formId) {
    if (element.getAttribute('data-submitting') === 'true') {
        return false;
    }
    element.setAttribute('data-submitting', 'true');
    element.style.pointerEvents = 'none';
    element.style.opacity = '0.5';
    element.innerText = 'Logging out...';

    const form = document.getElementById(formId);
    if (form) {
        form.submit();
    }
};