// Select DOM elements
const container = document.querySelector('.container');
const signupButton = document.querySelector('.signup-section header');
const loginButton = document.querySelector('.login-section header');

// Add event listener for click on the login button
loginButton.addEventListener('click', () => {
    // Add the 'active' class to the container
    container.classList.add('active');
});

// Add event listener for click on the signup button
signupButton.addEventListener('click', () => {
    // Remove the 'active' class from the container
    container.classList.remove('active');
});
