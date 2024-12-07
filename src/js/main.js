// Simuler l'appel aux APIs PHP via AJAX
document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    loadOrders();
});

function loadProducts() {
    fetch('getProducts.php')
        .then(response => response.json())
        .then(data => {
            let productSelect = document.getElementById('productSelect');
            let productList = document.getElementById('productList');
            productSelect.innerHTML = '';
            productList.innerHTML = '';

            data.forEach(product => {
                let option = document.createElement('option');
                option.value = product.IdProduit;
                option.textContent = product.Libellé + ' - €' + product.Prix;
                productSelect.appendChild(option);

                let productDiv = document.createElement('div');
                productDiv.innerHTML = `<strong>${product.Libellé}</strong><br>${product.Description}<br>€${product.Prix}`;
                productList.appendChild(productDiv);
            });
        })
        .catch(error => console.error('Error loading products:', error));
}

let cart = [];

function addToCart() {
    const productSelect = document.getElementById('productSelect');
    const selectedProductId = productSelect.value;

    fetch('getProduct.php?id=' + selectedProductId)
        .then(response => response.json())
        .then(data => {
            const product = data[0];
            cart.push(product);
            updateCart();
        })
        .catch(error => console.error('Error adding to cart:', error));
}

function updateCart() {
    const cartList = document.getElementById('cart');
    cartList.innerHTML = '';
    
    cart.forEach(item => {
        const li = document.createElement('li');
        li.textContent = item.Libellé + ' - €' + item.Prix;
        cartList.appendChild(li);
    });
}

function placeOrder() {
    const orderDate = new Date().toISOString().slice(0, 19).replace('T', ' ');
    const clientId = 1; // Simuler un ID de client
    fetch('addCommande.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            date: orderDate,
            idClient: clientId,
            cart: cart.map(item => item.IdProduit),
        })
    })
    .then(response => response.json())
    .then(data => {
        alert('Commande passée avec succès !');
        cart = [];
        updateCart();
        loadOrders();
    })
    .catch(error => console.error('Error placing order:', error));
}

function loadOrders() {
    fetch('getCommandes.php')
        .then(response => response.json())
        .then(data => {
            let orderList = document.getElementById('orderList');
            orderList.innerHTML = '';
            
            data.forEach(order => {
                let orderDiv = document.createElement('div');
                orderDiv.innerHTML = `<strong>Commande ${order.IdCommande}</strong> - ${order.Status} - Livraison : ${order.DateLivraison}`;
                orderList.appendChild(orderDiv);
            });
        })
        .catch(error => console.error('Error loading orders:', error));
}
