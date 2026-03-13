# Multi-Gateway Payment API - BeTalent Tech

Este projeto é a implementação do teste prático para a BeTalent Tech. Embora eu me identifique como **desenvolvedor iniciante**, optei por desafiar meus limites e implementar requisitos dos **Níveis 2 e 3**, focando em uma arquitetura modular e escalável.

## Stack Tecnológica

- PHP 8.4.6: Utilizando as últimas funcionalidades da linguagem (como property hooks e asymetrical visibility).
- Laravel 12.53: Versão mais recente do framework, garantindo performance e segurança.
- Docker: Ambiente isolado para MySQL e Mocks dos Gateways.
- Sanctum: Autenticação robusta para API.

## Diferenciais Implementados (Nível 2 e 3)

- Cálculo de Valor no Back-end: O valor da transação não é enviado pelo usuário, mas calculado com base no preço do produto cadastrado (Segurança Financeira).
- Fallback Automático: Se o Gateway A falhar, o sistema tenta o Gateway B automaticamente.
- Arquitetura de Adapters: Facilidade extrema para adicionar um terceiro ou quarto gateway apenas criando uma nova classe que respeita a `PaymentGatewayInterface`.
- RBAC (Role-Based Access Control): Controle de acesso rigoroso por níveis (Admin, Finance, Manager, User).
- Documentação Profissional: Código comentado seguindo padrões de mercado.

## Desafios e Aprendizados

Esta foi minha primeira experiência profunda desenvolvendo exclusivamente como API. Minha experiência anterior era focada em aplicações Laravel tradicionais (com Blade e Views).
Dificuldades superadas:

- Entender a persistência de tokens (Sanctum) em vez de sessões.
- Lógica de transformação de dados para JSON.
- Orquestração de serviços externos via HTTP dentro de um fluxo de fallback.

## Rotas Principais

### Públicas

- `POST /api/login`: Autenticação e geração de token.
- `GET /api/products`: Listagem de produtos disponíveis.
- `POST /api/purchase`: Realização de compra (Cálculo via back-end).

### Privadas (Requer Token Bearer)

- `POST /api/transactions/{id}/refund`: Estorno (Apenas ADMIN/FINANCE).
- `GET /api/transactions`: Listagem com filtros (Apenas ADMIN/FINANCE).

## Instalação

1. Clone o repositório.
2. Certifique-se de que os mocks de gateways estejam rodando nos endereços: `http://localhost:3001` e `http://localhost:3002`.
3. Execute `composer install`.
4. Configure o `.env` com seu banco MySQL.
5. `php artisan migrate --seed`.
6. `php artisan serve`.
