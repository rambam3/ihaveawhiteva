<?php
final class dbHandler
{
    private $dataSource = "mysql:host=localhost;dbname=wordle"; //Hier dient je connection string te komen mysql:dbname=;host=;
    private $username = "root";
    private $password = "";

    public function selectAll()
    {
        try{
            //Maak een nieuwe PDO
            //Voer het statement uit
            //Return een associatieve array met alle opgehaalde data.
            $conn = new PDO($this->dataSource, $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $result = $conn->query("SELECT * FROM `category` INNER JOIN word ON word.categoryID = category.categoryID;");
            $allArray = array();

            foreach($result as $row)
            {
                $allArray[] = array(
                    "wordId" => $row["wordId"],
                    "name" => $row["name"],
                    "text" => $row["text"],
                );
            }
            return $allArray;
        }
        catch(PDOException $exception){
            
            echo 'Error: '. $exception->getMessage();
            return false;
        }
    }

    public function selectCategories()
    {
        try{
            //Hier doe je grootendeels hetzelfde als bij SelectAll, echter selecteer je alleen alles uit de category tabel.
            $conn = new PDO($this->dataSource, $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $result = $conn->query("SELECT * FROM `category`");
            $categoriesArray = array();

            foreach($result as $row)
            {
                $categoriesArray[] = array(
                    "id" => $row["categoryId"],
                    "category" => $row["name"]    
                );
            }      
            return $categoriesArray;
        }
        catch(PDOException $exception){
            //Indien er iexts fout gaat kun je hier de exception var_dumpen om te achterhalen wat het probleem is.
            //Return false zodat het script waar deze functie uitgevoerd wordt ook weet dat het misgegaan is.

            echo 'Error: '. $exception->getMessage();
            return false;
        }
    }

    public function createWord($text, $categoryId)
    {
        try{
            $conn = new PDO($this->dataSource, $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //Maak een nieuwe PDO
            $result = $conn->prepare("INSERT INTO word (categoryId, text) VALUES (:categoryId, :text)");
            //Maak gebruik van de prepare functie van PDO om een insert into uit te voeren op de tabel word.
            
            $result->bindParam(":text", $text);
            $result->bindParam(":categoryId", $categoryId);
            //De kolommen die gevuld moeten worden zijn text en categoryId
            //Gebruik binding om de parameters aan de juiste kolommen te koppellen
            $result->execute();
            //Voer het statement uit
            //Return een associatieve array met alle opgehaalde data.
            return true;
            //Voer de query uit en return true omdat alles goed is gegaan
        }
        catch(PDOException $exception){
            //Indien er iets fout gaat kun je hier de exception var_dumpen om te achterhalen wat het probleem is.
            //Return false zodat het script waar deze functie uitgevoerd wordt ook weet dat het misgegaan is.
            echo "Error". $exception->getMessage();
            return false;
        }
    }

    public function selectOne($wordId){
        try{
            $conn = new PDO($this->dataSource, $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //Maak een nieuwe PDO
            $sql = $conn->prepare("SELECT * FROM word INNER JOIN category ON category.categoryId = word.categoryId WHERE wordId = :wordId" );
            $sql->bindParam(':wordId', $wordId);
            //Maak gebruik van de prepare functie van PDO om een select uit te voeren van 1 woord. Degene met het opgegeven Id
            //Let op dat de categorie wederom gejoined moet worden, en de wordId middels een parameter moet worden gekoppeld.
            $sql->execute();
            //Voer het statement uit  
            //maak een variabele $rows met een associatieve array met alle opgehaalde data.
            //we willen enkel 1 resultaat ophalen dus zorg dat de eerste regel van de array wordt gereturned.
           
            $word = $sql -> fetch(PDO::FETCH_ASSOC); 

            return $word;
            
        }
        catch(PDOException $exception){
            echo 'Error: '. $exception->getMessage();
            return false;
        }
    }

    public function updateWord($wordId, $text, $category){
        try{
            //Maak een nieuwe PDO
            $conn = new PDO($this->dataSource, $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //Maak gebruik van de prepare functie van PDO om een update uit te voeren van 1 woord. Degene met het opgegeven Id
            $sql = $conn->prepare("UPDATE `word` SET text = :text, categoryId = :category WHERE wordId = :wordId");
            //Let op dat zowel de velden die je wilt wijzigen (categorie en text) met parameters gekoppeld moeten worden

            //De wordId gebruik je voor een WHERE statement.
            //bind alle 3 je parameters
            $sql->bindParam(":text", $text);
            $sql->bindParam(':wordId', $wordId);
            $sql->bindParam(':category', $category);
            //voer de query uit en return true.
            
            return $sql->execute();
        }
        catch(PDOException $exception){
            echo 'Error: '. $exception->getMessage();
            return false;
        }
    }

    public function deleteWord($id){
        try{
            //Maak een nieuwe PDO
            $conn = new PDO($this->dataSource, $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = $conn->prepare("DELETE FROM word WHERE wordId = :Id");
            $sql->bindParam(":Id", $id);
            $sql->execute();
            return true;
            //Maak gebruik van de prepare functie van PDO om een delete uit te voeren van 1 woord. Degene met het opgegeven Id
            //De wordId gebruik je voor een WHERE statement.
            //bind je parameter
            //voer de query uit en return true.
        }
        catch(PDOException $e){
            echo 'Error: '. $e->getMessage();
            return false;
        }

    }
}
