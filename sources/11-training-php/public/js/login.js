document.addEventListener('DOMContentLoaded', function() {
    const sessionId = localStorage.getItem('sessionId');
    if (!sessionId) {
        alert('Bạn chưa đăng nhập!');
        window.location.href = 'login.php';
        return;
    }

    fetch('view_user.php', {
        headers: {
            'Authorization': 'Bearer ' + sessionId
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            document.getElementById('username').textContent = data.user.name;
            document.getElementById('fullname').textContent = data.user.fullname;
            document.getElementById('email').textContent = data.user.email;
        } else {
            alert(data.message);
            window.location.href = 'login.php';
        }
    })
    .catch(err => console.error(err));
});