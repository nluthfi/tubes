document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');

    loginForm.addEventListener('submit', (event) => {
        event.preventDefault(); 

        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        console.log('Login diproses untuk:', username);
        alert('Login Berhasil! Mengalihkan ke Dashboard...');
        window.location.href = 'dashboard.php';
    });
});