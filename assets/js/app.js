let currentCemeteryId = null;

document.addEventListener('DOMContentLoaded', () => {
    loadCemeteries();
    showSection('dashboard');
});

function showSection(sectionId) {
    document.querySelectorAll('.section').forEach(s => s.classList.add('hidden'));
    document.getElementById(`section-${sectionId}`).classList.remove('hidden');
    
    if (sectionId === 'reports') loadReports();
    if (sectionId === 'dashboard') loadCemeteries();
    if (sectionId === 'cemeteries') loadAdminCemeteries(); 
}

async function loadCemeteries() {
    const response = await fetch('api/cemeteries.php');
    const cemeteries = await response.json();
    const container = document.getElementById('cemetery-list');
    container.innerHTML = cemeteries.map(c => `
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow cursor-pointer" onclick="openMap(${c.id}, '${c.nome}')">
            <h3 class="text-xl font-bold text-indigo-900">${c.nome}</h3>
            <p class="text-gray-500 mt-1">${c.cidade}</p>
            <div class="mt-4 text-indigo-600 font-semibold flex items-center">
                Ver Mapa 
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
        </div>
    `).join('');
}

async function openMap(id, name) {
    currentCemeteryId = id;
    document.getElementById('map-title').innerText = name;
    showSection('map');
    loadGraves(id);
}

async function loadGraves(cemeteryId) {
    const response = await fetch(`api/graves.php?cemetery_id=${cemeteryId}`);
    const graves = await response.json();
    const container = document.getElementById('mapa');
    container.innerHTML = graves.map(g => `
        <div class="cova status-${g.status}" onclick="openGraveDetails(${g.id}, '${g.numero}')" title="Cova ${g.numero}">
            ${g.numero}
        </div>
    `).join('');
}

async function loadReports() {
    try {
        const response = await fetch('api/reports.php');
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        
        const data = await response.json();
        console.log('Dados carregados:', data);
        
        // Stats Cards
        const statsContainer = document.getElementById('report-stats');
        statsContainer.innerHTML = `
        <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-blue-500">
            <p class="text-sm text-gray-500 font-bold uppercase">Total de Jazigos</p>
            <p class="text-3xl font-black text-gray-800">${data.total_graves}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-green-500">
            <p class="text-sm text-gray-500 font-bold uppercase">Total de covas</p>
            <p class="text-3xl font-black text-gray-800">${data.total_covas}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-green-500">
            <p class="text-sm text-gray-500 font-bold uppercase">Ocupados</p>
            <p class="text-3xl font-black text-gray-800">${data.occupied_graves}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500 font-bold uppercase">Livres</p>
            <p class="text-3xl font-black text-gray-800">${data.free_graves}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-red-500">
            <p class="text-sm text-gray-500 font-bold uppercase">Excederam Tempo</p>
            <p class="text-3xl font-black text-gray-800">${data.exceeded_time}</p>
        </div>
    `;

    // Table
    const tableBody = document.getElementById('report-table-body');
    tableBody.innerHTML = data.details.map(d => `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 font-bold text-gray-700">${d.numero}</td>
            <td class="px-6 py-4 text-gray-600">${d.cemiterio_nome}</td>
            <td class="px-6 py-4 text-gray-800 font-medium">${d.morto_nome || '<span class="text-gray-400 italic">Vazio</span>'}</td>
            <td class="px-6 py-4 text-gray-600">${d.data_falecimento || '-'}</td>
            <td class="px-6 py-4">
                <span class="px-3 py-1 rounded-full text-xs font-bold ${d.status_tempo === 'Excedido' ? 'bg-red-100 text-red-700' : d.status_tempo === 'Livre' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}">
                    ${d.status_tempo}
                </span>
            </td>
            <td class="px-6 py-4 font-mono text-sm">${d.tempo_restante}</td>
        </tr>
    `).join('');
    } catch (error) {
        console.error('Erro ao carregar relatórios:', error);
        const statsContainer = document.getElementById('report-stats');
        statsContainer.innerHTML = '<p class="col-span-5 text-red-500">Erro ao carregar dados</p>';
    }
}

// Modais e Cadastros
function openAddCemeteryModal() {
    const modal = document.getElementById('modal');
    document.getElementById('modal-title').innerText = 'Novo Cemitério';
    document.getElementById('modal-content').innerHTML = `
        <div class="space-y-4">
            <input type="text" id="cem-nome" placeholder="Nome do Cemitério" class="w-full p-3 border rounded-lg">
            <input type="text" id="cem-end" placeholder="Endereço" class="w-full p-3 border rounded-lg">
            <input type="text" id="cem-cid" placeholder="Cidade" class="w-full p-3 border rounded-lg">
        </div>
    `;
    document.getElementById('modal-save').onclick = saveCemetery;
    modal.classList.remove('hidden');
}

async function saveCemetery() {
    const data = {
        nome: document.getElementById('cem-nome').value,
        endereco: document.getElementById('cem-end').value,
        cidade: document.getElementById('cem-cid').value
    };
    
    // Adicionado: headers e verificação de resposta
    await fetch('api/cemeteries.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });
    
    closeModal();
    // Importante: Recarregar a lista para o novo item aparecer
    loadCemeteries(); 
    showSection('dashboard');
}
function openAddGraveModal() {
    const modal = document.getElementById('modal');
    document.getElementById('modal-title').innerText = 'Novo Jazigo';
    document.getElementById('modal-content').innerHTML = `
        <div class="space-y-4">
            <input type="text" id="grave-num" placeholder="Número da Cova" class="w-full p-3 border rounded-lg">
            <input type="text" id="grave-Tip" placeholder="Tipo de cova (vertical/horizontal)" class="w-full p-3 border rounded-lg">
            <input type="number" id="grave-cap" placeholder="Capacidade de Corpos" value="1" class="w-full p-3 border rounded-lg">
        </div>
    `;
    document.getElementById('modal-save').onclick = saveGrave;
    modal.classList.remove('hidden');
}

async function saveGrave() {
    const data = {
        cemiterio_id: currentCemeteryId,
        numero: document.getElementById('grave-num').value,
        capacidade_total: document.getElementById('grave-cap').value,
        tipo: document.getElementById('grave-Tip').value
    };
    await fetch('api/graves.php', {
        method: 'POST',
        body: JSON.stringify(data)
    });
    closeModal();
    loadGraves(currentCemeteryId);
}

async function openGraveDetails(id, numero) {
    const modal = document.getElementById('modal');
    document.getElementById('modal-title').innerText = `Jazigo ${numero}`;
    
    // Busca a lista de pessoas sepultadas neste jazigo
    const response = await fetch(`api/deceased.php?grave_id=${id}`);
    const ocupantes = await response.json();
    
    let htmlContent = '';

    // Parte 1: Listagem de quem já está lá
    if (ocupantes.length > 0) {
        htmlContent += `
            <div class="mb-6">
                <h4 class="text-sm font-bold text-gray-500 uppercase mb-2">Ocupantes Atuais</h4>
                <div class="space-y-2">
                   ${ocupantes.map(o => `
                <h4 class="text-sm font-bold text-gray-500 uppercase mb-2">Tipo de Cova: ${o.Tipo}</h4>
    <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-100 flex justify-between items-center">
        <div>
            <p class="font-bold text-indigo-900">${o.nome}</p>
            <p class="text-xs text-indigo-700">Sepultamento: ${new Date(o.data_sepultamento).toLocaleDateString('pt-BR')}</p>
        </div>
        <button onclick="deleteDeceased(${o.id}, ${id}, '${numero}')" class="text-red-600 hover:bg-red-100 p-2 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        </button>
    </div>
`).join('')}
                </div>
            </div>
            <hr class="my-4">
        `;
    }

    htmlContent += `
        <h4 class="text-sm font-bold text-gray-500 uppercase mb-2">Novo Sepultamento</h4>
        <div class="space-y-4">
            <input type="text" id="dead-nome" placeholder="Nome do Falecido" class="w-full p-3 border rounded-lg">
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-xs font-bold text-gray-500">Nascimento</label>
                    <input type="date" id="dead-nasc" class="w-full p-3 border rounded-lg">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500">Falecimento</label>
                    <input type="date" id="dead-fale" class="w-full p-3 border rounded-lg">
                </div>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500">Data Sepultamento</label>
                <input type="date" id="dead-sep" class="w-full p-3 border rounded-lg">
            </div>
        </div>
    `;

    document.getElementById('modal-content').innerHTML = htmlContent;
    document.getElementById('modal-save').onclick = () => saveDeceased(id);
    modal.classList.remove('hidden');
}

async function saveDeceased(graveId) {
    const nomeInput = document.getElementById('dead-nome');
    // Se o campo nome estiver vazio, não faz nada (evita salvamento acidental)
    if (!nomeInput.value.trim()) return;

    const data = {
        grave_id: graveId,
        nome: nomeInput.value,
        data_nascimento: document.getElementById('dead-nasc').value,
        data_falecimento: document.getElementById('dead-fale').value,
        data_sepultamento: document.getElementById('dead-sep').value
    };

    const res = await fetch('api/deceased.php', {
        method: 'POST',
        body: JSON.stringify(data)
    });

    const result = await res.json();
    if (result.status === 'success') {
        // Limpa os campos para evitar envios duplicados
        nomeInput.value = '';
        document.getElementById('dead-nasc').value = '';
        document.getElementById('dead-fale').value = '';
        document.getElementById('dead-sep').value = '';
        
        closeModal();
        loadGraves(currentCemeteryId);
    } else {
        alert(result.message);
    }
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
}

async function loadAdminCemeteries() {
    const response = await fetch('api/cemeteries.php');
    const cemeteries = await response.json();
    const container = document.getElementById('admin-cemetery-list');
    container.innerHTML = cemeteries.map(c => `
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800">${c.nome}</h3>
                <p class="text-sm text-gray-500">${c.endereco} - ${c.cidade}</p>
            </div>
            <div class="flex space-x-2">
                <button onclick="openMap(${c.id}, '${c.nome}')" class="text-indigo-600 hover:bg-indigo-50 px-3 py-1 rounded">Ver Mapa</button>
            </div>
        </div>
    `).join('');
}
async function deleteDeceased(id, graveId, graveNumero) {
    if (!confirm('Tem certeza que deseja remover este registro de sepultamento?')) return;

    const response = await fetch('api/delete_deceased.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    });

    const result = await response.json();
    if (result.status === 'success') {
        // Recarrega os detalhes do jazigo para mostrar a lista atualizada
        openGraveDetails(graveId, graveNumero);
        // Atualiza o mapa de cores
        loadGraves(currentCemeteryId);
    } else {
        alert('Erro ao excluir: ' + result.message);
    }
}
