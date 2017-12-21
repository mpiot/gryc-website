<?php

namespace AppBundle\Service;

class SequenceManipulator
{
    public function reverse($sequence)
    {
        return strrev($sequence);
    }

    public function complement($sequence)
    {
        $complementaryTable = [
            'A' => 'T',
            'T' => 'A',
            'U' => 'A',
            'G' => 'C',
            'C' => 'G',
            'Y' => 'R',
            'R' => 'Y',
            'S' => 'S',
            'W' => 'W',
            'K' => 'M',
            'M' => 'K',
            'B' => 'V',
            'D' => 'H',
            'H' => 'D',
            'V' => 'B',
            'N' => 'N',
        ];

        $bases = str_split($sequence, 1);
        foreach ($bases as &$base) {
            $base = mb_strtoupper($base);

            if (array_key_exists($base, $complementaryTable)) {
                $base = $complementaryTable[$base];
            } else {
                throw new \RuntimeException('The base doesn\'t exists.');
            }
        }

        return implode('', $bases);
    }

    public function reverseComplement($sequence)
    {
        return $this->complement($this->reverse($sequence));
    }

    public function isNucleotidicFasta($fasta)
    {
        $sequences = $this->fastaToSequencesArray($fasta);

        // Is a fasta of nucleotides or amino acids ?
        // To define it, count all letters, and do statistics (90% of atcg => nucleotides)
        // We define it on the first sequence, to avoid bug if user mix amino acids and nucleotides sequences
        // Get the number of a, t, c, g
        // The ascii code are:
        // 65 -> A, 97 -> a
        // 67 -> C, 99 -> c
        // 71 -> G, 103 -> g
        // 84 -> T, 116 -> t
        // 45 -> -
        $charsCount = count_chars($sequences[0]['sequence'], 1);
        // Remove '-' from the array
        unset($charsCount[45]);

        $nucCount = 0;
        $totalCount = 0;

        foreach ($charsCount as $i => $val) {
            // Add the number of chars to the totalCount
            $totalCount += $val;

            // If the letter is a a, t, c, g, add it to the nucCount
            if (in_array($i, [65, 97, 67, 99, 71, 103, 84, 116], true)) {
                $nucCount += $val;
            }
        }

        // Then decide, is it nuc or prot ? (if percentage of acgt is > to 90 % => nuc)
        if (($nucCount / $totalCount) > 0.9) {
            return true;
        }

        return false;
    }

    public function fastaToSequencesArray($fasta, $delimiter = "\r\n")
    {
        // Separate different sequences by >
        $sequences = explode('>', $fasta);

        // Remove the first line, always empty
        unset($sequences[0]);
        $sequences = array_values($sequences);

        // loop on sequences
        foreach ($sequences as &$sequence) {
            // Explode the sequence on newline
            $explodedSequence = explode(PHP_EOL, $sequence);

            $sequence = [];
            $sequence['name'] = array_shift($explodedSequence);
            $sequence['sequence'] = implode($explodedSequence);
        }

        return $sequences;
    }

    public function arrayToFasta($sequencesArray)
    {
        $i = 0;
        $fasta = '';
        foreach ($sequencesArray as $sequence) {
            if (0 !== $i) {
                $fasta .= "\n\n";
            }

            $fasta .= '>'.$sequence['name']."\n";

            $sequence60 = str_split($sequence['sequence'], 60);
            foreach ($sequence60 as $seq60) {
                $fasta .= $seq60."\n";
            }

            ++$i;
        }

        return $fasta;
    }

    public function processManipulation($fasta, $action)
    {
        // Transform the fasta in an array
        $sequences = $this->fastaToSequencesArray($fasta);

        // For each sequence proceed to the selected action
        foreach ($sequences as &$sequence) {
            switch ($action) {
                case 'reverse-complement':
                    $sequence['name'] = $sequence['name'].'.rev-comp';
                    $sequence['sequence'] = $this->reverseComplement($sequence['sequence']);

                    break;

                case 'reverse':
                    $sequence['name'] = $sequence['name'].'.rev';
                    $sequence['sequence'] = $this->reverse($sequence['sequence']);

                    break;

                case 'complement':
                    $sequence['name'] = $sequence['name'].'.comp';
                    $sequence['sequence'] = $this->complement($sequence['sequence']);

                    break;
            }
        }

        return $this->arrayToFasta($sequences);
    }
}
