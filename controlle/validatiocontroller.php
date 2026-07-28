 <?php
require_once __DIR__ . '/../DAO/validationdao.php';
require_once __DIR__ . '/../DAO/internshipdao.php';
require_once __DIR__ . '/../model/validation.php';

class ValidationController {
    private $validationDAO;
    private $internshipDAO;

    public function __construct($db) {
        $this->validationDAO = new ValidationDAO($db);
        $this->internshipDAO = new InternshipDAO($db);
    }

    public function handleRequest() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';

        switch ($action) {
            case 'add':
                $this->add();
                break;
            case 'edit':
                $this->edit();
                break;
            case 'delete':
                $this->delete();
                break;
            default:
                $this->list();
                break;
        }
    }

    private function list() {
        $validations = $this->validationDAO->getAllValidations();
        // On récupère les stages pour les lier à une nouvelle soutenance
        $internships = $this->internshipDAO->getAllInternships();
        require_once __DIR__ . '/../view/validation_view.php';
    }

    private function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $internship_id = (int)($_POST['internship_id'] ?? 0);
            $defense_date = !empty($_POST['defense_date']) ? $_POST['defense_date'] : null;
            $jury_members = !empty($_POST['jury_members']) ? $_POST['jury_members'] : null;
            $final_grade = !empty($_POST['final_grade']) ? (float)$_POST['final_grade'] : null;
            $status = $_POST['status'] ?? 'En cours';

            $validation = new Validation(null, $internship_id, $defense_date, $jury_members, $final_grade, $status);
            
            if ($this->validationDAO->createValidation($validation)) {
                header("Location: dashbord.php?page=validations");
                exit();
            } else {
                $error = "Erreur lors de l'ajout de la validation.";
                $this->list();
            }
        }
    }

    private function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $internship_id = (int)($_POST['internship_id'] ?? 0);
            $defense_date = !empty($_POST['defense_date']) ? $_POST['defense_date'] : null;
            $jury_members = !empty($_POST['jury_members']) ? $_POST['jury_members'] : null;
            $final_grade = !empty($_POST['final_grade']) ? (float)$_POST['final_grade'] : null;
            $status = $_POST['status'] ?? 'En cours';

            $validation = new Validation($id, $internship_id, $defense_date, $jury_members, $final_grade, $status);
            
            if ($this->validationDAO->updateValidation($validation)) {
                header("Location: dashbord.php?page=validations");
                exit();
            } else {
                $error = "Erreur lors de la modification.";
                $this->list();
            }
        }
    }

    private function delete() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $this->validationDAO->deleteValidation($id);
        }
        header("Location: dashbord.php?page=validations");
        exit();
    }
}
?>