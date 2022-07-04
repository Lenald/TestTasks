<?php

/**
 * Tech task:
 * Write a program which would get as input some 4-digits amount of money
 * And write it in words like
 *     "одна тысяча шестьсот семьдесят три доллара"
 *     "двести один доллар"
 * and so on. With correct words endings
 * 
 * Run as CLI application
 */

declare(strict_types=1);

namespace Adorosh\Example;

class MoneyAmount
{
    private const DOLLAR_ROOT = 'доллар';
    private const DOLLAR_ENDING = ['', 'а', 'ов'];
    private const GRAND_ROOT = 'тысяч';
    private const GRAND_ENDING = ['а', 'и', ''];
    private const DICTIONARY = [
        1 => ['', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
        10 => [
            '',
            'десять',
            'двадцать',
            'тридцать',
            'сорок',
            'пятьдесят',
            'шестьдесят',
            'семьдесят',
            'восемьдесят',
            'девяносто'
        ],
        100 => ['', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот'],
        //optionally u can set '' instead of 'одна' to get strings like 'тысяча долларов'
        1000 => ['', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять']
    ];
    private const TEEN_DICTIONARY = [
        11 => 'одинадцать',
        12 => 'двенадцать',
        13 => 'тринадцать',
        14 => 'четырнадцать',
        15 => 'пятнадцать',
        16 => 'шеснадцать',
        17 => 'семнадцать',
        18 => 'восемнадцать',
        19 => 'девятнадцать'
    ];

    private array $amount = [];

    public function run(): void
    {
        $this->setAmount($this->askNumber());
        $this->write($this->generateOutput());
    }

    private function askNumber(): string
    {
        //using echo instead of readline prompt, coz we need \n inside
        $this->write('Please, enter a positive amount of money up to 9999. Only numbers are allowed!', '> ');
        $input = (string)readline();

        if (!$this->isValid($input)) {
            $this->write(PHP_EOL . 'An error during application run: Invalid output!');
            exit;
        }

        return $input;
    }

    private function isValid(string $input): bool
    {
        $intInput = (int)$input;

        return !(
            $input === ''
            || !is_numeric($input)
            || $input !== (string)$intInput
            || $intInput < 0
            || $intInput > 9999
        );
    }

    private function setAmount(string $amount): void
    {
        $index = 1;

        while (!empty($amount)) {
            $this->amount[$index] = (int)substr($amount, -1, 1); //get last character
            $index *= 10;
            $amount = substr($amount, 0, -1); //cut last character we already processed
        }
    }

    private function generateOutput(): string
    {
        $amount = (int)implode('', array_reverse($this->amount));
        $output = $this->processTeenCases($amount);

        foreach ($this->amount as $numberPlace => $numberDigit) {
            $adjustmentToOutput = $this::DICTIONARY[$numberPlace][$numberDigit];

            if ($numberPlace === 1000 && $numberDigit !== 0) {
                $adjustmentToOutput .= ' ' . $this::GRAND_ROOT . $this->getEnding($this::GRAND_ENDING, $numberDigit);
            }

            $output = trim("$adjustmentToOutput $output");
        }

        if (empty(trim($output))) {
            $output = 'ноль';
        }

        return  PHP_EOL . $this->mbUcfirst(
            $output . ' ' . $this::DOLLAR_ROOT . $this->getEnding($this::DOLLAR_ENDING, $amount)
        );
    }

    private function getEnding(array $dictionary, int $number): string
    {
        if ($number >= 100) {
            $number %= 100;
        }

        if ($number >= 11 && $number <= 14) {
            $key = 2;
        } else {
            if ($number >= 10) {
                $number %= 10;
            }

            if ($number === 1) {
                $key = 0;
            } elseif ($number >= 2 && $number <= 4) {
                $key = 1;
            } else {
                $key = 2;
            }
        }

        return $dictionary[$key];
    }

    private function processTeenCases($amount): string
    {
        $amount %= 100;

        if ($amount >= 11 && $amount <= 19) {
            $output = $this::TEEN_DICTIONARY[$amount];
            unset($this->amount[1], $this->amount[10]);
        }

        return $output ?? '';
    }

    private function write(string $output, string $postfix = ''): void
    {
        echo $output . PHP_EOL . $postfix;
    }

    private function mbUcfirst(string $row): string
    {
        $encoding = mb_detect_encoding($row);
        $first = mb_substr($row, 0, 1, $encoding);
        $rest = mb_substr($row, 1, null, $encoding);
        
        return mb_strtoupper($first, $encoding) . $rest;
    }
}

(new MoneyAmount())->run();
