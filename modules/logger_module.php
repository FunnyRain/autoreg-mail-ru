<?php

function outputString(array $messages = []): void {
  echo PHP_EOL . '[' . date('H:i:s, d.m.y') . ']' . implode("\n> ", $messages);
}
