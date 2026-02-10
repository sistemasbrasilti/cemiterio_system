<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Cemitério Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-indigo-900 flex items-center justify-center min-h-screen py-12">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Criar Cadastro</h2>
        <div class="space-y-4">
            <input type="text" id="nome" placeholder="Nome completo" class="w-full p-3 border rounded-lg">
            <input type="email" id="email" placeholder="Email" class="w-full p-3 border rounded-lg">
            <input type="password" id="senha" placeholder="Senha" class="w-full p-3 border rounded-lg">
            <input type="password" id="confirmaSenha" placeholder="Confirmar Senha" class="w-full p-3 border rounded-lg">
            <button onclick="fazerCadastro()" class="w-full bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700 transition">Cadastrar</button>
            <p id="msg" class="text-red-500 text-sm text-center hidden"></p>
            <p class="text-gray-600 text-sm text-center mt-4">Já possui uma conta? <a href="login.php" class="text-indigo-600 hover:underline">Fazer login.</a></p>
        </div>
    </div>

    <script>
    async function fazerCadastro() {
        const nome = document.getElementById('nome').value;
        const email = document.getElementById('email').value;
        const senha = document.getElementById('senha').value;
        const confirmaSenha = document.getElementById('confirmaSenha').value;

        if (!nome || !email || !senha || !confirmaSenha) {
            alert('Preencha todos os campos');
            return;
        }

        if (senha !== confirmaSenha) {
            alert('As senhas não coincidem');
            return;
        }

        if (senha.length < 6) {
            alert('A senha deve ter pelo menos 6 caracteres');
            return;
        }

        try {
            const res = await fetch('api/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ nome, email, senha })
            });

            const result = await res.json();
            if (result.status === 'success') {
                alert('Cadastro realizado com sucesso! Faça login agora.');
                window.location.href = 'login.php';
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
