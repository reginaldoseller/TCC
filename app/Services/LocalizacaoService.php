<?php

namespace App\Services;

/**
 * Classe responsável por gerenciar a lógica de geolocalização e integração com APIs externas (ViaCEP e Nominatim/OpenStreetMap).
 * Isenta as Controllers de regras de negócio complexas, centralizando a busca de endereços e coordenadas.
 */
class LocalizacaoService
{
    /**
     * Consulta os dados de endereço a partir de um CEP utilizando a API pública do ViaCEP.
     * 
     * @param string $cep CEP a ser consultado (com ou sem máscara)
     * @return array|null Retorna um array com logradouro, bairro, cidade e estado, ou null em caso de erro.
     */
    public function consultarCep(string $cep): ?array
    {
        // Remove caracteres não numéricos do CEP (deixa apenas os números)
        $cepLimpo = preg_replace('/[^0-9]/', '', $cep);

        // Valida se o CEP possui exatamente 8 dígitos
        if (strlen($cepLimpo) !== 8) {
            return null;
        }

        // Monta a URL da API do ViaCEP
        $url = "https://viacep.com.br/ws/{$cepLimpo}/json/";

        // Utiliza o CURLRequest nativo do CodeIgniter para fazer a requisição HTTP de forma segura
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->get($url, [
                'timeout' => 5,
                'verify'  => false // <--- ADICIONE ESTA LINHA PARA EVITAR ERRO DE SSL NO WAMP/XAMPP LOCAL
            ]);

            // Verifica se a requisição foi bem-sucedida (status HTTP 200)
            if ($response->getStatusCode() === 200) {
                $dados = json_decode($response->getBody(), true);

                // O ViaCEP retorna um array com a chave 'erro' se o CEP não existir na base
                if (isset($dados['erro']) && $dados['erro'] === true) {
                    return null;
                }

                // Retorna os dados normalizados focando no que a plataforma precisa
                return [
                    'cep'        => $dados['cep'] ?? '',
                    'logradouro' => $dados['logradouro'] ?? '',
                    'bairro'     => $dados['bairro'] ?? '',
                    'cidade'     => $dados['localidade'] ?? '',
                    'estado'     => $dados['uf'] ?? '', 
                ];
            }
        } catch (\Exception $e) {
            // Se quiser debugar algum erro futuro, pode descomentar a linha abaixo:
            // log_message('error', 'Erro CEP: ' . $e->getMessage());
            return null;
        }

        return null;
    }

    /**
     * Obtém a latitude e a longitude aproximadas de um endereço utilizando a API pública do Nominatim (OpenStreetMap).
     * 
     * @param string $bairro Bairro do endereço
     * @param string $cidade Cidade
     * @param string $estado Sigla do estado (UF)
     * @return array|null Retorna um array com 'latitude' e 'longitude' ou null caso não encontre.
     */
    public function obterCoordenadas(string $bairro, string $cidade, string $estado): ?array
    {
        // Monta a string de consulta focando na região
        $query = trim("{$bairro}, {$cidade}, {$estado}, Brasil");

        $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($query) . "&limit=1";

        $client = \Config\Services::curlrequest();

        try {
            $response = $client->get($url, [
                'headers' => [
                    'User-Agent' => 'GetNinjaApp/1.0 (contato@seudominio.com)' 
                ],
                'timeout' => 5,
                'verify'  => false // <--- ADICIONE ESTA LINHA PARA EVITAR BLOQUEIO SSL LOCAL
            ]);

            if ($response->getStatusCode() === 200) {
                $resultados = json_decode($response->getBody(), true);

                if (!empty($resultados) && isset($resultados[0]['lat']) && isset($resultados[0]['lon'])) {
                    return [
                        'latitude'  => (float) $resultados[0]['lat'],
                        'longitude' => (float) $resultados[0]['lon'],
                    ];
                }
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}