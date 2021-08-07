<?php


class Parser {
    public const ID_FORMAT_PRE_2016 = 1;
    public const ID_FORMAT_2016 = 2;
    private $data_components = [];



    public function parse(string $id_number) {
        $id_number = $this->checkLength($id_number);
		if($id_number == ''){
			return false;
		}
        if(!$this->checkBirthDate($id_number)){
			return false;
		}
		
		return true;
		
        
    }

    private function checkLength(string $id_number): string {
        $id_number = strtoupper($id_number);
        $strlen = strlen($id_number);

        if ($strlen === 10) {
            if ($id_number[9] !== 'V') {
                //throw new InvalidArgumentException('Ending character is invalid.', 103);
				return false;
            }
            $id_number = substr($id_number, 0, 9);
        }

        if (!ctype_digit($id_number)) {
            //throw new InvalidArgumentException('Provided number is not all-numeric', 102);
			return false;
        }

        return (string)$id_number;
    }

    private function checkBirthDate(string $id_number) {
        $full_number = strlen($id_number) === 9
            ? '19'.$id_number
            : $id_number;

        $year = (int)substr($full_number, 0, 4);
        $this->data_components['year'] = $year;
        if(!$this->checkBirthYear($year)){
			return false;
		}
        $this->buildBirthDateObject($full_number, $year);
        $this->data_components['serial'] = (string)substr($full_number, 7);
		return true;
    }

    private function checkBirthYear(int $year) {
        if ($year < 1900 || $year > 2100) {
            //throw new InvalidArgumentException('Birth year is out ff 1900-2100 range', 200);
			return false;
        }else {
			return true;
		}
    }

    private function buildBirthDateObject(string $full_number, int $year) {
        $birthday = new DateTime();
        $birthday->setDate($year, 1, 1)->setTime(0, 0);
        $birth_days_since = (int)substr($full_number, 4, 3);

        if ($birth_days_since > 500) {
            $birth_days_since -= 500;
            $this->data_components['gender'] = 'F';
        } else {
            $this->data_components['gender'] = 'M';
        }

        --$birth_days_since;
        if (date('L', mktime(0, 0, 0, 1, 1, $year)) !== '1') {
            --$birth_days_since;
        }
		if($birth_days_since == -1){
			return false;
		}

        $birthday->add(new DateInterval('P'.$birth_days_since.'D'));
        $this->data_components['date'] = $birthday;
        if ($birthday->format('Y') !== (string)$year) {
            //throw new InvalidArgumentException('Birthday indicator is invalid.', 201);
			return false;
        }
    }



}


$test = new Parser;

var_dump($test->parse('970452338v'));


?>