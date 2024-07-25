let cards = '<div class="card-group">';
data.forEach(user => {
    cards += `
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">${user.nombre}</h5>
            <p class="card-text">${user.email}</p>
        </div>
    </div>`;
});
cards += '</div>';
document.getElementById("userData").innerHTML = cards;
