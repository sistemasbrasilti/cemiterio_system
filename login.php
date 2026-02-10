<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Cemitério Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-indigo-900 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Acesso ao Sistema</h2>
        <div class="space-y-4">
            <input type="email" id="email" placeholder="Email" class="w-full p-3 border rounded-lg">
            <input type="password" id="senha" placeholder="Senha" class="w-full p-3 border rounded-lg">
            <button onclick="fazerLogin()" class="w-full bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700 transition">Entrar</button>
            <p id="msg" class="text-red-500 text-sm text-center hidden"></p>
            <p class="text-gray-600 text-sm text-center mt-4">Não possui uma conta? <a href="register.php" class="text-indigo-600 hover:underline">Faça seu cadastro.</a></p>
        </div>
    </div>

    <script>
    async function fazerLogin() {
        console.log("Botão clicado!"); 
        
        const email = document.getElementById('email').value;
        const senha = document.getElementById('senha').value;

        try {
            const res = await fetch('api/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, senha })
            });

            const result = await res.json();
            if (result.status === 'success') {
                window.location.href = 'index.php';
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error("Erro na requisição:", error);
            alert("Erro ao conectar com o servidor.");
        }
    }
    </script>
</body>
</html>