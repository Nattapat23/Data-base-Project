

<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sign Up - Luminix</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Times New Roman', Times, serif, sans-serif;
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #000000, #8b0909, #000000, #8b0909);
      background-size: 300% 300%;
      transition: background-position 0.2s ease;
      overflow-y: auto;
    }

    .card {
      width: 100%;
      max-width: 420px;
      padding: 28px;
      margin: 20px;
      border-radius: 14px;
      background: rgba(22, 22, 22, 0.7);
      box-shadow: 0 8px 30px rgba(0,0,0,0.7);
      color: #fff;
    }

    .logo {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 20px;
    }

    .logo img {
      width: 200px;
      height: 200px;
      object-fit: contain;
      border-radius: 50%;
    }

    h2 {
      color: #ffffff;
      margin: 0 0 16px 0;
      text-align: center;
      font-weight: 500;
    }

    .row {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .field {
      display: flex;
      flex-direction: column;
      margin-bottom: 14px;
      flex: 1;
    }

    label {
      font-size: 14px;
      margin-bottom: 5px;
      color: rgba(255,255,255,0.8);
    }

    .field input {
      padding: 12px;
      border-radius: 8px;
      border: 1px solid rgba(255,255,255,0.08);
      background: rgba(255,255,255,0.05);
      color: #fff;
      outline: none;
      transition: border 0.2s, background 0.2s;
    }

    .field input:focus {
      border-color: #ff4d4f;
      background: rgba(255,255,255,0.1);
    }

    .btn {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      border: 0;
      background: #ff4d4f;
      color: #fff;
      cursor: pointer;
      font-weight: 500;
      font-size: 15px;
      transition: background 0.2s, transform 0.1s;
    }

    .btn:hover {
      background: #ff6666;
      transform: translateY(-1px);
    }

    .muted {
      font-size: 13px;
      color: rgba(255,255,255,0.65);
      margin-top: 14px;
      text-align: center;
    }

    a.link {
      color: #ffb3b3;
      text-decoration: none;
      transition: color 0.2s;
    }

    a.link:hover {
      color: #ffffff;
    }

    .error {
      color: #ff9b9b;
      font-size: 13px;
      margin-bottom: 8px;
      display: none;
      text-align: center;
    }
  </style>
</head>
<body>
  <script>
    document.addEventListener('mousemove', e => {
      const { innerWidth, innerHeight } = window;
      const x = (e.clientX / innerWidth) * 100;
      const y = (e.clientY / innerHeight) * 100;
      document.body.style.backgroundPosition = `${x}% ${y}%`;
    });
  </script>
  
  <div class="card">
    <div class="logo">
      <img src="img/logo.png" alt="Luminix Logo">
    </div>

    <h2>Sign Up</h2>
    <div id="err" class="error">Error message</div>

    <div class="row">
      <div class="field">
        <label>First Name</label>
        <input id="firstname" type="text" placeholder="First Name">
      </div>
      <div class="field">
        <label>Last Name</label>
        <input id="lastname" type="text" placeholder="Last Name">
      </div>
    </div>

    <div class="field">
      <label>Email</label>
      <input id="email" type="email" placeholder="you@gmail.com">
    </div>

    <div class="field">
      <label>Password</label>
      <input id="password" type="password" placeholder="Password">
    </div>

    <div class="field">
      <label>Confirm Password</label>
      <input id="password2" type="password" placeholder="Confirm Password">
    </div>

    <button id="signupBtn" class="btn">Sign Up</button>

    <div class="muted">
      Already have an account? 
      <a class="link" href="login.html">Sign in here</a>
    </div>
  </div>

  <script>
(function(){
  const signupBtn = document.getElementById('signupBtn');
  const errEl = document.getElementById('err');

  signupBtn.addEventListener('click', async function(){
    errEl.style.display = 'none';

    const firstname = document.getElementById('firstname').value.trim();
    const lastname = document.getElementById('lastname').value.trim();
    const email = document.getElementById('email').value.trim().toLowerCase();
    const password = document.getElementById('password').value;
    const password2 = document.getElementById('password2').value;

    // Validation
    if(!firstname || !lastname || !email || !password || !password2){
      return showError('Please fill in all required fields');
    }
    if(password !== password2){
      return showError('Passwords do not match');
    }
    if(password.length < 6){
      return showError('Password must be at least 6 characters');
    }

    // Submit data
    try {
      const formData = new FormData();
      formData.append('firstname', firstname);
      formData.append('lastname', lastname);
      formData.append('email', email);
      formData.append('password', password);

      const res = await fetch('signup_api.php', {
        method: 'POST',
        body: formData
      });
      const data = await res.json();

      if(data.success){
        alert('Registration successful! Please sign in.');
        window.location.href = 'login.html';
      } else {
        showError(data.message || 'Registration failed');
      }
    } catch (err){
      console.error(err);
      showError('Cannot connect to server');
    }
  });

  function showError(txt){
    errEl.textContent = txt;
    errEl.style.display = 'block';
  }
})();
</script>
  
</body>
</html>