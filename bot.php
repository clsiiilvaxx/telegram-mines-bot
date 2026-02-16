<?php

$token = "8419787382:AAEHFYx2mvyY5Z9BGAV9I9zthoea0yBvW4U";
$chat_id = "-1003791063521";
$linkJogar = "https://seulink.com";

// =====================================
// LOOP INFINITO
// =====================================

while(true){

    // =====================================
    // DEFINIR DIFICULDADE BALANCEADA
    // =====================================

    // Bombas: 3 a 5 (máximo 5)
    $qtdBombas = rand(3,5);

    // Diamantes para abrir: 4 a 8 (máximo 8)
    $qtdAbrir = rand(4,8);

    // Garantir que não ultrapasse 25 casas
    if($qtdBombas + $qtdAbrir > 25){
        $qtdAbrir = 25 - $qtdBombas;
    }

    // Sortear posições seguras (diamantes)
    $diamantes = [];
    while(count($diamantes) < $qtdAbrir){
        $pos = rand(0,24);
        if(!in_array($pos, $diamantes)){
            $diamantes[] = $pos;
        }
    }

    // =====================================
    // CRIAR IMAGEM GRID 5x5
    // =====================================

    $tamanho = 100;
    $espaco = 5;
    $colunas = 5;
    $linhas = 5;

    $largura = ($tamanho * $colunas) + ($espaco * ($colunas + 1));
    $altura = ($tamanho * $linhas) + ($espaco * ($linhas + 1));

    $imagem = imagecreatetruecolor($largura, $altura);

    $cinzaFundo = imagecolorallocate($imagem, 190, 190, 190);
    $cinzaQuadrado = imagecolorallocate($imagem, 220, 220, 220);
    $borda = imagecolorallocate($imagem, 40, 40, 40);

    imagefill($imagem, 0, 0, $cinzaFundo);

    $bombaImg = imagecreatefrompng("bomba.png");
    $diamanteImg = imagecreatefrompng("diamante.png");

    $contador = 0;

    for ($linha = 0; $linha < $linhas; $linha++) {
        for ($coluna = 0; $coluna < $colunas; $coluna++) {

            $x = $espaco + ($coluna * ($tamanho + $espaco));
            $y = $espaco + ($linha * ($tamanho + $espaco));

            imagefilledrectangle($imagem, $x, $y, $x+$tamanho, $y+$tamanho, $cinzaQuadrado);
            imagerectangle($imagem, $x, $y, $x+$tamanho, $y+$tamanho, $borda);

            if(in_array($contador, $diamantes)){

                // DIAMANTE (CASA PARA ABRIR)
                imagecopyresampled(
                    $imagem,
                    $diamanteImg,
                    $x+15,
                    $y+15,
                    0,
                    0,
                    $tamanho-30,
                    $tamanho-30,
                    imagesx($diamanteImg),
                    imagesy($diamanteImg)
                );

            } else {

                // BOMBA (NÃO ABRIR)
                imagecopyresampled(
                    $imagem,
                    $bombaImg,
                    $x+15,
                    $y+15,
                    0,
                    0,
                    $tamanho-30,
                    $tamanho-30,
                    imagesx($bombaImg),
                    imagesy($bombaImg)
                );
            }

            $contador++;
        }
    }

    // SALVAR IMAGEM
    $arquivo = "sinal.png";
    imagepng($imagem, $arquivo);
    imagedestroy($imagem);

    // =====================================
    // ENVIAR PARA TELEGRAM
    // =====================================

    $mensagem = "<b>💎 SINAL CL MINES 💎</b>\n\n"
              . "💣 <b>Bombas:</b> $qtdBombas\n"
              . "💎 <b>Casas para abrir:</b> $qtdAbrir\n\n"
              . "🎯 Abra somente os diamantes!\n"
              . "🔥 Gerencie sua banca.";

    $keyboard = [
        "inline_keyboard" => [
            [
                ["text" => "🎰 Jogar Aqui", "url" => $linkJogar]
            ]
        ]
    ];

    $postFields = [
        "chat_id" => $chat_id,
        "photo" => new CURLFile(realpath($arquivo)),
        "caption" => $mensagem,
        "parse_mode" => "HTML",
        "reply_markup" => json_encode($keyboard)
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot$token/sendPhoto");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_exec($ch);
    curl_close($ch);

    // Espera 2 minutos
    sleep(120);
}
