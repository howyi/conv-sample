<?php

namespace App\Command;

use Conv\DatabaseStructureFactory;
use Conv\MigrationGenerator;
use Conv\Operator;
use Conv\Structure\TableStructureInterface;
use Phpmig\Console\PhpmigApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this->setName('generate');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $phpmig = (new PhpmigApplication())->find('migrate');
        $phpmig->run(new ArrayInput([]), $output);

        $filter = function (TableStructureInterface $table) {
            return !in_array($table->getName(), ['migrations'], true);
        };

        $pdo = new \PDO('mysql:host=127.0.0.1','root','');
        $schemaDbStructure = DatabaseStructureFactory::fromSqlDir(
            $pdo,
            'database',
            $operator = new Operator($this->getHelper('question'), $input, $output),
            $filter
        );
        $dbStructure = DatabaseStructureFactory::fromPDO($pdo, 'conv_sample', $filter);

        $alterMigrations = MigrationGenerator::generate(
            $dbStructure,
            $schemaDbStructure,
            $operator
        );
        $generatedContents = [];
        $i = (new \GlobIterator('./migrations/*.php'))->count();
        foreach ($alterMigrations->getMigrationList() as $migration) {
            $fileName = date('YmdHis') . "_C$i";
            $className = "C" . $i;
            $up = $migration->getUp();
            $down = $migration->getDown();
            $content = <<<EOL
<?php

use Phpmig\Migration\Migration;

class $className extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        \$sql = <<<SQL
$up
SQL;
        \$container = \$this->getContainer();
        \$container['db']->query(\$sql);
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        \$sql = <<<SQL
$down
SQL;
        \$container = \$this->getContainer();
        \$container['db']->query(\$sql);
    }
}

EOL;
            $generatedContents["$fileName.php"] = $content;
            $i++;
        }


        $operator->output("\n");

        if (0 !== count($alterMigrations->getMigrationList())) {

            foreach ($alterMigrations->getMigrationList() as $migration) {
                $operator->output("<fg=green>### TABLE NAME: {$migration->getTableName()}</>");
                $operator->output('<fg=yellow>--------- UP ---------</>');
                $operator->output("<fg=blue>{$migration->getUp()}</>");
                $operator->output('<fg=yellow>-------- DOWN --------</>');
                $operator->output("<fg=magenta>{$migration->getDown()}</>\n\n");
            }
        }

        $count = count($alterMigrations->getMigrationList());
        $operator->output("<fg=green>Generated $count migrations</>");

        if (0 !== count($generatedContents)) {
            foreach ($generatedContents as $filename => $content) {
                file_put_contents("./migrations/$filename", $content);
            }
        }
    }
}
