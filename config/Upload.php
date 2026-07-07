<?php

// Trata o upload de imagens dos produtos: confere tipo e tamanho,
// da um nome novo ao ficheiro (para nunca haver dois iguais) e
// move para assets/uploads/.
class Upload
{
    private const TIPOS_PERMITIDOS = ['image/jpeg', 'image/png', 'image/webp'];
    private const TAMANHO_MAXIMO = 2 * 1024 * 1024; // 2MB

    public static function imagem(array $ficheiro): string|false
    {
        if ($ficheiro['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if ($ficheiro['size'] > self::TAMANHO_MAXIMO) {
            return false;
        }

        $tipo = mime_content_type($ficheiro['tmp_name']);

        if (!in_array($tipo, self::TIPOS_PERMITIDOS, true)) {
            return false;
        }

        $extensao = pathinfo($ficheiro['name'], PATHINFO_EXTENSION);
        $nomeFicheiro = uniqid('produto_') . '.' . strtolower($extensao);
        $destino = __DIR__ . '/../assets/uploads/' . $nomeFicheiro;

        if (!move_uploaded_file($ficheiro['tmp_name'], $destino)) {
            return false;
        }

        return $nomeFicheiro;
    }
}
