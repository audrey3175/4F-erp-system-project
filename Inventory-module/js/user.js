document.addEventListener("DOMContentLoaded", () => {
    const activeUser = JSON.parse(localStorage.getItem('foodsync_active_user'));
    if (activeUser) {
        const namaLengkap = activeUser.firstName + " " + activeUser.lastName;
        const topbarNameElement = document.getElementById('topbarUserName');
        if (topbarNameElement) {
            topbarNameElement.textContent = namaLengkap;
        }
    }
});