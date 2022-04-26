<?php
  $latte = new Latte\Engine;
  $latte->setTempDirectory(__DIR__."/../../latteTemp");
  $params = [
    'data' => $data,
  ];
  // render to output
  $latte->render(__DIR__.'/widget.latte', $params);
?>
