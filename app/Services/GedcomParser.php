<?php
/**
 * php-gedcom is a library for parsing, manipulating, importing and exporting
 * GEDCOM files in PHP 8.3+, used for parsing aspects of GEDCOM files in this project.
 *
 * @license         MIT
 *
 * @link            https://github.com/liberu-genealogy/php-gedcom
 */
namespace App\Services;

use Gedcom\Parser;
use App\Models\Person;
use App\Models\Spouse;
use App\Models\MotherAndChild;
use App\Models\FatherAndChild;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

/**
 * Contains the parsing logic required to extract individuals' details and relationship information and convert (if necessary) to store in database accordingly.
 */
class GedcomParser
{
    /**
    * Removes all existing data for given family tree ID.
    */
    private function deleteFamilyTreeData($familyTreeId)
    {
        MotherAndChild::where('family_tree_id', $familyTreeId)->delete();
        
        FatherAndChild::where('family_tree_id', $familyTreeId)->delete();
        
        Spouse::where('family_tree_id', $familyTreeId)->delete();
        
        Person::where('family_tree_id', $familyTreeId)->delete();
    } 
 /**
 * Parses the file - individuals' information is extracted via parser library php-gedcom,
 * and manual parsing is used to extract relationship information by splitting and reading each line, 
 * and identifying tags relevant for extraction.
 * 
 * @param string $filePath Path to the GEDCOM file
 * @param int $familyTreeId ID of the family tree being processed
 */
    public function parse($filePath, $familyTreeId)
    {
        $this->familyTreeId = $familyTreeId; 
        $this->deleteFamilyTreeData($familyTreeId);
        
        //Parser (php-gedcom) extracts the file which is uploaded on website
        $parser = new Parser();
        $gedcom = $parser->parse($filePath);
        //for loop to identify all individuals in the file via getIndi ('INDI' tag is used for individuals)
        foreach ($gedcom->getIndi() as $individual) {
            //getName used to obtain names ('NAME' tag is used for names)
            $names = $individual->getName();
            if (!empty($names)) { //if names array is not empty (individual may have multiple names/aliases)
                $name = reset($names); //retrieves first name of individual
                $givenName = $name->getGivn();
                $surname = $name->getSurn();                
                if (!empty($givenName) || !empty($surname)) {
                    $fullName = trim($givenName . ' ' . $surname);
                } else {
                    $fullName = $name->getName();
                }
                $fullName = str_replace('/', '', $fullName);
                if (empty($surname)) {
                    $nameParts = explode(' ', trim($fullName));
                    $surname = end($nameParts);
                }
                $birth = $individual->getBirt(); //retrieves birth information via getBirt ('BIRT' tag is used for this)
                $death = $individual->getDeat(); //retrieves death information via getDeat ('DEAT' tag is used for this)
                $birthPlace = $birth ? $birth->getPlac() : null;
                $deathPlace = $death ? $death->getPlac() : null;
                /**retrieves the birth and death dates for individual through getDate ('DATE' tag is used for this)
                 * method extractQual is called and the birth/death dates are passed as parameters for this if they are not null
                 * otherwise null is passed
                */
                $birthDate = $this->extractQual($birth ? $birth->getDate() : null); 
                $deathDate = $this->extractQual($death ? $death->getDate() : null);
                
                //corresponding information is then passed to storePerson method
                $this->storePerson(
                    $this->familyTreeId,
                    $individual->getId(), //obtains GEDCOM ID for individual (characterised by @Ixxxx@)
                    $fullName, //obtains and concatenates first name and surname
                    $surname,
                    $individual->getSex(), //obtains gender
                    $birthDate['date'], //DOB obtained from extractQual (after conversion via getDate)
                    $birthDate['qualifier'], //qualifier (ABT, BEF, AFT, used when dates are approximate) obtained from extractQual associated with DOB
                    $deathDate['date'], //DOD obtained from extractQual (after conversion via getDate)
                    $deathDate['qualifier'], //qualifier (ABT, BEF, AFT, used when dates are approximate) obtained from extractQual associated with DOD
                    $birthPlace,
                    $deathPlace
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
        
                $columns = explode(' ', $line, 3); //splits each line into 3 columns via spaces (only first 2 spaces are counted)
                if (count($columns) < 2) continue; //skips lines which have less than 2 columns
                $level = (int)$columns[0]; //first column is converted to integer (as each line of GEDCOM files begin with a number)
                $tag = $columns[1]; //next column is used to retrieve the tag (e.g. 'NAME', 'BIRT', 'MARR')
                $value = isset($columns[2]) ? trim($columns[2]) : ''; //final column is used to retrieve the data corresponding to the tag, whilst removing whitespaces at the start and end
        
                if ($level === 0 && strpos($tag, '@F') === 0) { //if level is 0 and the tag begins with '@F' (indicating family)
                    if (isset($id)) { //stores previous family record when new one is found (spouse, mother-child and father-child info)
                        $this->storeSpouses(
                            $this->familyTreeId,
                            $id,
                            $mother_id ?? null,
                            $father_id ?? null,
                            $marriageDate['date'] ?? null,
                            $marriageDate['qualifier'] ?? null, 
                            $divorceDate['date'] ?? null,
                            $divorceDate['qualifier'] ?? null
                        );
                        $this->storeMotherAndChild(
                            $this->familyTreeId,
                            $id,
                            $mother_id ?? null,
                            $child_id ?? null,
                            $child_number ?? null,
                            $isAdopted ?? false
                        );
                        $this->storeFatherAndChild(
                            $this->familyTreeId,
                            $id,
                            $father_id ?? null,
                            $child_id ?? null,
                            $child_number ?? null,
                            $isAdopted ?? false
                        );
                    }
                    $id = trim($tag, '@'); //extracts ID and removes '@'
                    //initialising variables by setting to null
                    $mother_id = null;
                    $father_id = null;
                    $child_id = null;
                    $child_number = null;
                    $marriageDate = null;
                    $divorceDate = null;
                } elseif ($level === 1) { //if level is 1
                 if ($tag === 'MARR') { //if tag equals 'MARR' (indicating marriage)
                    $isMarried = true; //sets isMarried bool to true
                 } elseif ($tag === 'DIV') { //if tag equals 'DIV' (indicating divorce)
                    $isDivorced = true; //sets isDivorced bool to true
                 } elseif ($tag === 'HUSB') { //if tag equals 'HUSB' (indicating husband)
                    $father_id = trim($value, '@'); //extracts husband's individual ID
                 } elseif ($tag === 'WIFE') { //if tag equals 'WIFE' (indicating wife)
                    $mother_id = trim($value, '@'); //extracts wife's individual ID
                 } elseif ($tag === 'CHIL') { //if tag equals 'CHIL' (indicating child)
                    $child_id = trim($value, '@'); //extracts child's individual ID
                    $child_number++; //increases number of children by one for families with multiple children
                    $isAdopted = false; // initialise adoption status
                 } elseif ($tag === 'ADOP') {
                    $isAdopted = true; // mark child as adopted
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
            
            if (isset($id)) { //passes all data extracted of final family record to storeSpouse, storeMotherAndChild and storeFatherAndChild methods
                $this->storeSpouses(  
                $this->familyTreeId,
                $id,
                $mother_id ?? null,
                $father_id ?? null,
                $marriageDate['date'] ?? null,
                $marriageDate['qualifier'] ?? null, 
                $divorceDate['date'] ?? null,
                $divorceDate['qualifier'] ?? null
            );
            $this->storeMotherAndChild(
                $this->familyTreeId,
                $id,
                $mother_id ?? null,
                $child_id ?? null,
                $child_number ?? null,
                $isAdopted ?? false,
            );
            $this->storeFatherAndChild(
                $this->familyTreeId,
                $id,
                $father_id ?? null,
                $child_id ?? null,
                $child_number ?? null,
                $isAdopted ?? false
            );
            }
        }
    }
    /**
    * Stores the extracted information from the parser into the People table, creating or updating a Person record within it.
    */
    private function storePerson($familyTreeId, $gedcomId, $name, $surname, $gender, $birth, $birthDateQualifier, $death, $deathDateQualifier, $birthPlace, $deathPlace)
    {
        $person = Person::create([ //updates/creates Person record with corresponding information for each column in People table
            'gedcom_id' => $gedcomId,
            'family_tree_id' => $familyTreeId,
            'name' => $name,
            'gender' => $gender,
            'surname' => $surname,
            'birth_date' => $this->convertToDate($birth),
            'birth_date_qualifier' => $birthDateQualifier,
            'death_date' => $this->convertToDate($death),
            'death_date_qualifier' => $deathDateQualifier,
            'birth_place' => $birthPlace,
            'death_place' => $deathPlace
        ]);
        /** 
         * try-catch flashes a message through Laravel's Session class 
         *(\Facades\Session - Facades are equivalent of wrapper classes for Laravel, where it instantiates the class and resolves any dependencies behind the scenes) 
         * indicating whether the method was performed successfully or failed.
         * if failed, throws exception
         */
        try {
            Session::flash('success', "Person created/updated: " . $name . "(".$gedcomId.").");
        } catch (\Exception $e) {
            Session::flash('error', "Error storing person: " . $name . "(".$gedcomId."). Error - " . $e->getMessage());
        }
    }
    /**
    * Stores the extracted information from the parser into the Spouses table, creating or updating a Spouse record within it.
    */
            private function storeSpouses($familyTreeId, $gedcomId, $mother_id, $father_id, $marriageDate, $marriageDateQualifier, $divorceDate, $divorceDateQualifier)
            {
                if ($mother_id && $father_id){ //if mother_id and father_id were extracted searches for respective GEDCOM IDs in People table and obtains details
                    $mother = Person::where('gedcom_id', $mother_id)->first();
                    $father = Person::where('gedcom_id', $father_id)->first();
                    
                    if ($mother && $father) { //if mother's and father's details are available through matching IDs will update/create Spouse record with corresponding information for each column in Spouses table
                        $spouseRelationship = Spouse::updateOrCreate(
                    ['gedcom_id' => $gedcomId],
                    [   'family_tree_id' => $familyTreeId,
                        'first_spouse_id' => $mother->id,
                        'second_spouse_id' => $father->id,
                        'marriage_date' => $this->convertToDate($marriageDate),
                        'marriage_date_qualifier' => $marriageDateQualifier,
                        'divorce_date' => $this->convertToDate($divorceDate),
                        'divorce_date_qualifier' => $divorceDateQualifier
                    ]
                );
                }
            }
        }
    /**
    * Stores the extracted information from the parser into the MotherAndChildren table, creating or updating a Mother/Child record within it.
    */
            private function storeMotherAndChild($familyTreeId, $gedcomId, $mother_id, $child_id, $child_number, $isAdopted)
            {
                if ($mother_id && $child_id) { //if mother_id and child_id were extracted searches for respective GEDCOM IDs in People table and obtains details
                    $mother = Person::where('gedcom_id', $mother_id)->first();
                    $child = Person::where('gedcom_id', $child_id)->first();
                    if ($mother && $child){ //if mother's and child's details are available through matching IDs will update/create Mother/Child record with corresponding information for each column in MotherAndChildren table
                    $motherAndChildRelationship = MotherAndChild::updateOrCreate(
                        ['gedcom_id' => $gedcomId . '-CHILD '.$child_number], //concatenates the family GEDCOM ID with "-CHILD" string and the child's number (appropriate for families with multiple children as only the last child is stored otherwise due to overwriting the same GEDCOM ID)
                        [   'family_tree_id' => $familyTreeId, 
                            'mother_id' => $mother->id,
                            'child_id' => $child->id,
                            'child_number' => $child_number,
                            'is_adopted' => $isAdopted
                        ]
                    );
                }
            }
        }
    /**
    * Stores the extracted information from the parser into the FatherAndChildren table, creating or updating a Father/Child record within it.
    */
        private function storeFatherAndChild($familyTreeId, $gedcomId, $father_id, $child_id, $child_number, $isAdopted)
        {
                if ($father_id && $child_id) { //if father_id and child_id were extracted searches for respective GEDCOM IDs in People table and obtains details
                $father = Person::where('gedcom_id', $father_id)->first();
                $child = Person::where('gedcom_id', $child_id)->first();
                if ($father && $child){ //if father's and child's details are available through matching IDs will update/create Father/Child record with corresponding information for each column in FatherAndChildren table
                $fatherAndChildRelationship = FatherAndChild::updateOrCreate(
                ['gedcom_id' => $gedcomId. '-CHILD '.$child_number],
                [   'family_tree_id' => $familyTreeId,
                    'father_id' => $father->id,
                    'child_id' => $child->id,
                    'child_number' => $child_number,
                    'is_adopted' => $isAdopted
                ]
            );
            }
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
                if (strpos($gedcomDate, $qual) === 0) { //if any of the qualifiers are found at the beginning of a date, sets that specific qualifier to qualifier variable
                    $qualifier = $qual;
                    $date = trim(str_replace($qual, '', $gedcomDate)); //extracts the date by removing the qualifier, replacing with space, then removing the space
                    break;
                }
            } //if there is no qualifier date is simply set as the date found
            if (!$qualifier) {
                $date = $gedcomDate;
                $qualifier = 'EXACT';
            }
        }

        //returns array containing the date (converted via convertToDate) and qualifier
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
            } catch (\Exception $e) { //flashes error and throws exception if date cannot be parsed/formatted - STILL IN PROGRESS
                Session::flash('error', "Error converting date. Error - " . $e->getMessage());
                return null;
            }
        }
        return null;
    }
}