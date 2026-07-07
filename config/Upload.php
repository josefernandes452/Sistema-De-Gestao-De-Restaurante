<?php

// Trata o upload de imagens dos produtos: confere tipo e tamanho,
// da um nome novo ao ficheiro (para nunca haver dois iguais) e
// move para assets/uploads/.
class Upload
{
    // O valor de cada entrada e a extensao com que o ficheiro fica
    // gravado. Nunca usamos o nome que o utilizador mandou para isso,
    // senao dava para mandar um ficheiro com bytes de imagem valida
    // mas chamado "shell.php" e ele ficava gravado como .php dentro
    // de assets/uploads/ (e um .php ali dentro corre como pagina).
    private const TIPOS_PERMITIDOS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
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

        if (!isset(self::TIPOS_PERMITIDOS[$tipo])) {
            return false;
        }

        $extensao = self::TIPOS_PERMITIDOS[$tipo];
        $nomeFicheiro = uniqid('produto_') . '.' . $extensao;
        $destino = __DIR__ . '/../assets/uploads/' . $nomeFicheiro;

        if (!move_uploaded_file($ficheiro['tmp_name'], $destino)) {
            return false;
        }

        return $nomeFicheiro;
    }
}
