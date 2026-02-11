<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestão de Cemitérios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .cova { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; cursor: pointer; transition: transform 0.2s; }
        .cova:hover { transform: scale(1.1); }
        .status-verde { background-color: #10b981; }
        .status-amarelo { background-color: #f59e0b; }
        .status-vermelho { background-color: #ef4444; }
        .mapa-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(50px, 1fr)); gap: 15px; background: white; padding: 20px; border-radius: 10px; border: 1px solid #e5e7eb; }
    </style>
</head>
<body class="bg-gray-50">
    <nav class="bg-indigo-900 text-white p-4 shadow-xl">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-extrabold tracking-tight">Cemitério Digital</h1>
            <ul class="flex space-x-8 font-medium">
                <li><a href="#" onclick="showSection('dashboard')" class="hover:text-indigo-300">Início</a></li>
                <li><a href="#" onclick="showSection('cemeteries')" class="hover:text-indigo-300">Cemitérios</a></li>
                <li><a href="#" onclick="showSection('reports')" class="hover:text-indigo-300">Relatórios</a></li>
            </ul>
        </div>
    </nav>

    <main class="container mx-auto mt-10 p-4">
        <!-- Dashboard -->
        <section id="section-dashboard" class="section ">
            <div id="search-container" class="flex space-x-2 mb-4">
                <input type="text" id="search-input" placeholder="Procurar pessoa..." class="p-3 border rounded-lg">
                <button onclick="searchPeople()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-semibold shadow-md">Procurar</button>
            </div>
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Painel de Controle</h2>
                <p class="text-gray-600">Selecione um cemitério para visualizar o mapa e gerenciar as covas.</p>
            </div>
            <div id="cemetery-list" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Preenchido via JS -->
            </div>
        </section>

        <!-- Mapa do Cemitério -->
        <section id="section-map" class="section hidden">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h2 id="map-title" class="text-3xl font-bold text-gray-800">Mapa</h2>
                    <p id="map-subtitle" class="text-gray-600">Gestão de jazigos e ocupação.</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="openAddGraveModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-semibold shadow-md">+ Novo Jazigo</button>
                    <button onclick="showSection('dashboard')" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-lg font-semibold">Voltar</button>
                </div>
            </div>
            
            <div class="bg-white p-4 rounded-xl shadow-sm mb-6 flex flex-wrap gap-6 items-center border border-gray-100">
                <span class="flex items-center gap-2"><span class="w-4 h-4 rounded-full status-verde"></span> Livre</span>
                <span class="flex items-center gap-2"><span class="w-4 h-4 rounded-full status-amarelo"></span> Ocupado (< 5 anos)</span>
                <span class="flex items-center gap-2"><span class="w-4 h-4 rounded-full status-vermelho"></span> Excedido (> 5 anos)</span>
            </div>

            <div id="mapa" class="mapa-container shadow-inner min-h-[400px]">
                <!-- Jazigos renderizados aqui -->
            </div>
        </section>

        <!-- Relatórios -->
        <section id="section-reports" class="section hidden">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Relatórios e Estatísticas</h2>
            <div id="report-stats" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            </div>
            
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                <div class="p-6 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-700">Listagem Detalhada de Jazigos</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-xs uppercase font-bold tracking-wider">
                                <th class="px-6 py-4 text-left">Cova</th>
                                <th class="px-6 py-4 text-left">Cemitério</th>
                                <th class="px-6 py-4 text-left">Falecido</th>
                                <th class="px-6 py-4 text-left">Data Falec.</th>
                                <th class="px-6 py-4 text-left">Status Tempo</th>
                                <th class="px-6 py-4 text-left">Restante</th>
                            </tr>
                        </thead>
                        <tbody id="report-table-body" class="divide-y divide-gray-100">
                            <!-- Preenchido via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Configurações de Cemitérios -->
        <section id="section-cemeteries" class="section hidden">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Gerenciar Unidades</h2>
                <button onclick="openAddCemeteryModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-semibold shadow-md">+ Novo Cemitério</button>
            </div>
            <div id="admin-cemetery-list" class="grid grid-cols-1 gap-4">
                <!-- Lista para edição -->
            </div>
        </section>
    </main>

    <!-- Modal Genérico -->
    <div id="modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col max-h-[90vh]">
            <div class="p-6 border-b border-gray-100 flex-shrink-0">
                <h3 id="modal-title" class="text-xl font-bold text-gray-800">Título</h3>
            </div>
            <div id="modal-content" class="p-6 overflow-y-auto">
                
            </div>
            <div class="p-6 bg-gray-50 flex justify-end space-x-3">
                <button onclick="closeModal()" class="px-5 py-2 text-gray-600 font-medium hover:text-gray-800">Voltar</button>
                <button id="modal-save" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-bold shadow-lg">Salvar</button>
            </div>
        </div>
    </div>

    <script src="assets/js/app.js?v=<?= time() ?>"></script>
</body>
</html>
