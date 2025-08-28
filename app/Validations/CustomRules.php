<?php

namespace App\Validations;
use App\Models\DocumentDetailModel;
use App\Models\ActionModel;
use App\Models\ActionTakenModel;
use App\Models\UserModel;
use App\Models\DocumentTypeModel;

class CustomRules
{
    public $documentdetailmodel;
    public $actionmodel;
    public $actiontakenmodel;
    public $usermodel;
    public $documenttypemodel;

    public function __construct()

    {

        $this->documentdetailmodel = new DocumentDetailModel();
        $this->actionmodel = new ActionModel();
        $this->actiontakenmodel = new ActionTakenModel();
        $this->usermodel = new UserModel();
        $this->documenttypemodel = new DocumentTypeModel();
    }

    public function requiredIfValue(string $value, string $field, array $data, string $requiredValue): bool
    {
        $triggerField = $data[$field]; // The field that triggers the condition
        $currentField = $data[$value]; // The field that is required conditionally
        

        if ($triggerField === $requiredValue && empty($currentField)) {
            return false;
        }

        return true;
    }


    public function requiredIfExternal(string $value, string $params, array $data): bool
    {
        // Split the parameters to get the field name to check
        $fieldToCheck = explode(',', $params)[0];

        // Check if the specified field is 'external'
        if (isset($data[$fieldToCheck]) && $data[$fieldToCheck] === 'E') {
            return !empty($value); // Return true if the current field has a value
        }

        return true; // If not, validation passes
    }


    public function checkIfOfficeExists(string $selectedoffice, string $params): bool
    {
        list($routeno, $controlno) = array_pad(explode(',', $params), 2, null);
        
        $detail = $this->documentdetailmodel->checkIfDestinationExistsValidation($routeno,$selectedoffice,$controlno);
        
        if ($detail) {
            return true;
        }

        return false;
    }

    public function checkActionTakenExists(string $selectedactionTaken): bool
    {
        return $this->actionmodel->checkActionTaken($selectedactionTaken);
    }

    public function usernameUnique(string $str, ?string $fields = null, array $data = []): bool
    {
        // Return false if it exists (not unique)
        return !$this->usermodel->action_officer_exists($str);
    }

    public function doctypeUnique(string $str, ?string $fields = null, array $data = []): bool
    {
        $idToIgnore = $fields;
        // Return false if it exists (not unique)
        return !$this->documenttypemodel->document_type_exists($str, $idToIgnore);
    }

    public function actionrequireUnique(string $str, ?string $fields = null, array $data = []): bool
    {
        $idToIgnore = $fields;
        // Return false if it exists (not unique)
        return !$this->actionmodel->action_required_exists($str, $idToIgnore);
    }

    public function actiontakenUnique(string $str, ?string $fields = null, array $data = []): bool
    {
        $idToIgnore = $fields;
        // Return false if it exists (not unique)
        return !$this->actiontakenmodel->action_taken_exists($str, $idToIgnore);
    }


    

}