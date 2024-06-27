<?php
/**
 * php-gedcom is a library for parsing, manipulating, importing and exporting
 * GEDCOM 5.5 files in PHP 5.3+, used for parsing aspects of GEDCOM files in this project.
 *
 * @copyright       Copyright (c) 2010-2013, Kristopher Wilson
 * @license         MIT
 *
 * @link            http://github.com/mrkrstphr/php-gedcom
 */
namespace App\Services;

use Gedcom\Parser;
use App\Models\Person;
use App\Models\Relationship;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * WIP
 * Contains the parsing logic required to extract individuals' details and relationship information and convert (if necessary) to store in database accordingly.
 */
class GedcomParser
{
 /**
 * Parses the file - individuals' information is extracted via parser library php-gedcom,
 * and manual parsing is used to extract relationship information by splitting and reading each line, 
 * and identifying tags relevant for extraction.
 */
    public function parse($filePath)
    {
        //Parser (php-gedcom) extracts the file which is uploaded on website
        $parser = new Parser();
        $gedcom = $parser->parse($filePath);
        //for loop to identify all individuals in the file via getIndi ('INDI' tag is used for individuals)
        foreach ($gedcom->getIndi() as $individual) {
            //getName used to obtain names ('NAME' tag is used for names)
            $names = $individual->getName();
            if (!empty($names)) { //if names array is not empty (as it comprises of first and surnames)
                $name = reset($names); //retrieves first name associated with individual
                $birth = $individual->getBirt(); //retrieves birth information via getBirt ('BIRT' tag is used for this)
                $death = $individual->getDeat(); //retrieves death information via getDeat ('DEAT' tag is used for this)
                
                /**retrieves the birth and death dates for individual through getDate ('DATE' tag is used for this)
                 * method extractQual is called and the birth/death dates are passed as parameters for this if they are not null
                 * otherwise null is passed
                */
                $birthDate = $this->extractQual($birth ? $birth->getDate() : null); 
                $deathDate = $this->extractQual($death ? $death->getDate() : null);
                
                //corresponding information is then passed to storePerson method
                $this->storePerson(
                    $individual->getId(), //obtains GEDCOM ID for individual (characterised by @Ixxxx@)
                    $name->getSurn() . ', ' . $name->getGivn(), //obtains and concatenates surname and first name
                    $individual->getSex(), //obtains gender
                    $birthDate['date'], //DOB obtained from getDate
                    $birthDate['qualifier'], //qualifier (ABT, BEF, AFT, used when dates are approximate) obtained from extractQual associated with DOB
                    $deathDate['date'], //DOD obtained from getDate
                    $deathDate['qualifier'] //qualifier (ABT, BEF, AFT, used when dates are approximate) obtained from extractQual associated with DOD
                );
            }
        }
        //Alternative manual parsing for relationships
            $fileContent = file_get_contents($filePath); //reads file into string
            $lines = explode("\n", $fileContent); //splits string via delimiter "\n" (new line)
            
        //for loop going through each split line
            foreach ($lines as $line) {
                $line = trim($line); //removes whitespaces from beginning and end of line
                if (empty($line)) continue; //skips empty lines
        
                $columns = explode(' ', $line, 3); //splits each line into 3 columns via spaces
                if (count($columns) < 2) continue; //skips lines which have less than 2 columns
                $level = (int)$columns[0]; //first column is converted to integer (as each line of GEDCOM files begin with a number)
                $tag = $columns[1]; //next column is used to retrieve the tag (e.g. 'NAME', 'BIRT', 'MARR')
                $value = isset($columns[2]) ? trim($columns[2]) : ''; //final column is used to retrieve the data corresponding to the tag, whilst removing whitespaces at the start and end
        
                if ($level === 0 && strpos($tag, '@F') === 0) { //if level is 0 and the tag begins with '@F' (indicating family)
                    if (isset($id)) { //if family record exists already pass the relevant details to storeRelationship method before moving onto next
                        $this->storeRelationship(
                            $id,
                            $mother_id ?? null,
                            $father_id ?? null,
                            $child_id ?? null, 
                            $marriageDate['date'] ?? null,
                            $marriageDate['qualifier'] ?? null, 
                            $divorceDate['date'] ?? null,
                            $divorceDate['qualifier'] ?? null
                        );
                    }
                    $id = trim($tag, '@'); //extracts ID and removes '@'
                    $mother_id = null;
                    $father_id = null;
                    $child_id = null;
                    $marriageDate = null; //sets marriage date to null
                    $divorceDate = null; //sets divorce date to null
                } elseif ($level === 1) { //if level is 1
                 if ($tag === 'MARR') { //if tag equals 'MARR' (indicating marriage)
                    $isMarried = true; //sets isMarried bool to true
                 } elseif ($tag === 'DIV') { //if tag equals 'DIV' (indicating divorce)
                    $isDivorced = true; //sets isDivorced bool to true
                 } elseif ($tag === 'HUSB') {
                    $father_id = trim($tag, '@');
                 } elseif ($tag === 'WIFE') {
                    $mother_id = trim($tag, '@');
                 } elseif ($tag === 'CHIL') {
                    $child_id = trim($tag, '@');
                 }
                } elseif ($level === 2 && $tag === 'DATE'){ //if level is 2 and contains the tag 'DATE'
                    if (isset($isMarried)) { //if a defined value is found for isMarried (i.e. true)
                    $marriageDate = $this->extractQual($value); //extract marriage date and pass to convertToDate method
                    unset($isMarried); //removes value for isMarried
                } elseif (isset($isDivorced)) { //if a defined value is found for isDivorced (i.e. true)
                    $divorceDate = $this->extractQual($value); //extract divorce date and pass to convertToDate method
                    unset($isDivorced); //removes value for isDivorced
            }
        }
            if (isset($id)) { //passes all data extracted of new family record to storeRelationship method
                $this->storeRelationship(  
                $id,
                $mother_id ?? null,
                $father_id ?? null,
                $child_id ?? null, 
                $marriageDate['date'] ?? null,
                $marriageDate['qualifier'] ?? null, 
                $divorceDate['date'] ?? null,
                $divorceDate['qualifier'] ?? null
            );
            }
        }
    }
    /**
    * Stores the extracted information from the parser into the People table, creating or updating a Person record within it.
    */
    private function storePerson($gedcomId, $name, $gender, $birth, $birthDateQualifier, $death, $deathDateQualifier)
    {
        $person = Person::updateOrCreate( //updates/creates Person record with corresponding information for each column in People table
            ['gedcom_id' => $gedcomId],
            [
                'name' => $name,
                'gender' => $gender,
                'birth_date' => $this->convertToDate($birth),
                'birth_date_qualifier' => $birthDateQualifier,
                'death_date' => $this->convertToDate($death),
                'death_date_qualifier' => $deathDateQualifier,
            ]
        );
        /**try-catch logs a message through Laravel's logging system (\Facades\Log - Facades are equivalent of wrapper classes for Laravel) indicating 
         * whether the method was performed successfully or failed.
         * if failed, logs error message and throws exception
         */
        try {
        Log::info('Person created/updated', ['gedcom_id' => $gedcomId]);
        } catch (\Exception $e) {
        Log::error('Error storing person', ['gedcom_id' => $gedcomId, 'error' => $e->getMessage()]);
        throw $e; 
        }
    }
    /**
    * Stores the extracted information from the parser into the Relationships table, creating or updating a Relationship record within it.
    */
    private function storeRelationship($gedcomId, $mother_id, $father_id, $child_id, $marriageDate, $marriageDateQualifier, $divorceDate, $divorceDateQualifier)
    {
        if ($mother_id && $father_id){
        $spouseRelationship = Relationship::updateOrCreate(
            ['gedcom_id' => $gedcomId . '_SPOUSE'],
            [   'person_id' => $mother_id,
                'relative_id' => $father_id,
                'type' => 'spouse',
                'marriage_date' => $this->convertToDate($marriageDate),
                'marriage_date_qualifier' => $marriageDateQualifier,
                'divorce_date' => $this->convertToDate($divorceDate),
                'divorce_date_qualifier' => $divorceDateQualifier
            ]
        );
    } elseif ($mother_id && $child_id) {
        $motherAndChildRelationship = Relationship::updateOrCreate(
            ['gedcom_id' => $gedcomId . '_MOTHER-CHILD'],
            [   'person_id' => $mother_id,
                'relative_id' => $child_id,
                'type' => 'mother-child'
            ]
        );
    } elseif ($father_id && $child_id) {
        $fatherAndChildRelationship = Relationship::updateOrCreate(
        ['gedcom_id' => $gedcomId . '_FATHER-CHILD'],
        [   'person_id' => $father_id,
            'relative_id' => $child_id,
            'type' => 'father-child'
        ]
    );
}
        try {
        Log::info('Relationship created/updated', ['gedcom_id' => $gedcomId]);
        } catch (\Exception $e) {
        Log::error('Error storing relationship', ['gedcom_id' => $gedcomId, 'error' => $e->getMessage()]);
        throw $e; 
        }
    }
    /**
    * Extracts qualifiers ('ABT', 'BEF', 'AFT') used for approximation of dates from a given date if applicable. Separation of dates and qualifiers is required to allow storing of dates which contain both.
    */
    private function extractQual($gedcomDate){
        $qualifiers = ['ABT', 'BEF', 'AFT']; //sets qualifiers to extract
        $date = null; //date is set to null
        $qualifier = null; //qualifier is set to null

        if ($gedcomDate) { //if date exists in GEDCOM file
            foreach ($qualifiers as $qual) { //loops through each qualifier
                if (strpos($gedcomDate, $qual) === 0) { //if any of the qualifiers are found at the beginning of a date sets that specific qualifier to qualifier variable
                    $qualifier = $qual;
                    $date = trim(str_replace($qual, '', $gedcomDate)); //extracts the date by removing the qualifier, replacing with space
                    break;
                }
            } //if there is no qualifier date is simply set as the date found
            if (!$qualifier) {
                $date = $gedcomDate;
            }
        }

        //returns an array containing the date (converted via convertToDate) and qualifier
        return [
            'date' => $this->convertToDate($date),
            'qualifier' => $qualifier
        ];
    }

    /**
    * Converts the date found in GEDCOM file (usually formatted as 01 JAN 1900 for example) to a standard DateTime format acceptable for storing in MySQL databases, as they are not stored otherwise. 
    * Different edge cases of dates are considered (i.e. year only, month and year only, full date)
    * Utilises PHP's Carbon package (included in Laravel), an API extension used to manipulate date and time, formatted in the desired manner.
    */
    private function convertToDate($date)
    {
        if ($date) { //if date exists
            try {
                if (preg_match('/^\d{4}$/', $date)) { //if regular expression matches 4 digits i.e. year only
                    return Carbon::createFromFormat('Y', $date)->startOfYear()->format('Y-m-d'); //creates Carbon object (date) based on the year found, then formatted to be displayed as year-month-day, with date being set to first day of given year
                } elseif (preg_match('/^\d{4} \w{3}$/', $date)) { //if regular expression matches 4 digits with a space and 3 letters i.e. year and month only
                    return Carbon::createFromFormat('Y M', $date)->startOfMonth()->format('Y-m-d'); //creates Carbon object based on the year and month found, then formatted to be displayed as year-month-day, with date being set to first day of given month of given year
                } else { //otherwise the full date is found
                    return Carbon::parse($date)->format('Y-m-d'); //full date is formatted to year-month-day format
                }
            } catch (\Exception $e) { //logs error and throws exception if date cannot be parsed/formatted
                Log::error('Error parsing date', ['gedcom_date' => $date, 'error' => $e->getMessage()]);
                return null;
            }
        }
        return null;
    }
}