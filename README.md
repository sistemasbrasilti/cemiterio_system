# Sistema de Gestão de Cemitérios

Este sistema foi desenvolvido para gerenciar cemitérios municipais, permitindo o controle de jazigos, sepultamentos e o tempo de permanência dos corpos.

## Funcionalidades Principais
- **Cadastro de Cemitérios:** Gerencie múltiplas unidades na cidade.
- **Mapa Interativo:** Visualize a ocupação de cada jazigo através de cores:
  - **Verde:** Livre.
  - **Amarelo:** Ocupado (menos de 5 anos).
  - **Vermelho:** Excedido (mais de 5 anos - pronto para exumação).
- **Gestão de Jazigos:** Cadastro de covas com controle de capacidade.
- **Gestão de Falecidos:** Registro completo de dados e datas de sepultamento.
- **Relatórios:** Painel com estatísticas de ocupação e listagem detalhada com tempo restante para atingir o limite de 5 anos.

## Requisitos do Sistema
- PHP 8.1 ou superior
- MySQL 8.0 ou superior
- Servidor Web (Apache/Nginx)

## Instruções de Instalação
1. **Banco de Dados:**
   - Importe o arquivo `database.sql` no seu servidor MySQL.
   - Configure as credenciais de acesso em `config/database.php`.

2. **Servidor Web:**
   - Coloque os arquivos na pasta raiz do seu servidor (ex: `/var/www/html`).
   - Certifique-se de que as permissões de leitura estão corretas.

3. **Acesso:**
   - Acesse via navegador: `http://seu-dominio/login.php`.

## Estrutura do Projeto
- `/api`: Endpoints que processam os dados.
- `/assets`: Arquivos CSS e JavaScript.
- `/config`: Configurações de conexão.
- `index.php`: Interface principal do sistema.
