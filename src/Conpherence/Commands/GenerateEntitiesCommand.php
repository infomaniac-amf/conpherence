<?php
namespace Conpherence\Commands;

use Conpherence\Doctrine2\Bootstrap;
use Conpherence\Doctrine2\Formatter;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use MwbExporter\Writer\WriterInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;

class GenerateEntitiesCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'entities:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates entities based on a MySQL Workbench file';

    /**
     * The MySQL Workbench file to be analyzed for entity properties
     *
     * @var string
     */
    protected $workbenchFile;

    /**
     * The output path for generate entities
     *
     * @var string
     */
    protected $outputPath;

    /**
     * The output path for generate entities
     *
     * @var string
     */
    protected $generateDerived;

    /**
     * The list of tables to restrict the code generation to using
     *
     * @var array
     */
    protected $tablesRestrict;

    private $formatter;

    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        App::error(
            function (Exception $e) {
                echo($e);
                die();
            }
        );

        $path = $this->getPath($this->argument('mwb-file'));
        if (!realpath($path)) {
            $this->error('Cannot find MySQL Workbench file');
            return;
        }

        $this->workbenchFile = $path;
        $this->outputPath    = $this->getPath($this->argument('output-path'), true);

        if (!realpath($this->outputPath)) {
            @mkdir($this->outputPath, 0777, true);
        }

        $this->tablesRestrict = trim(strtolower($this->option('tables')));
        if (!empty($this->tablesRestrict)) {
            $this->tablesRestrict = explode(',', $this->tablesRestrict);
        } else {
            $this->tablesRestrict = array();
        }

        $this->generateDerived = trim(strtolower($this->option('generate-derived'))) == "true";
        if ($this->generateDerived) {
            $this->generateEntities();
        }
    }

    public function runManually($workbenchFile, $outputPath, $generateDerived = true, $tables = null)
    {
        $this->output = new ConsoleOutput();

        try {
            $this->workbenchFile = $workbenchFile;
            $this->outputPath    = $this->getPath($outputPath, true);

            if (!realpath($this->outputPath)) {
                @mkdir($this->outputPath, 0777, true);
            }

            $this->tablesRestrict = trim(strtolower($tables));
            if (!empty($this->tablesRestrict)) {
                $this->tablesRestrict = explode(',', $this->tablesRestrict);
            } else {
                $this->tablesRestrict = array();
            }

            $this->generateDerived = $generateDerived;
            if ($this->generateDerived) {
                $this->generateEntities();
            }
        } catch (Exception $e) {
            die();
        }
    }

    private function generateEntities()
    {
        $setup = array(
            Formatter::CFG_LOG_TO_CONSOLE        => true,
            Formatter::CFG_INDENTATION           => 4,
            Formatter::CFG_FILENAME              => 'Base%entity%.%extension%',
            Formatter::CFG_ENTITY_NAMESPACE      => 'Conpherence\\Entities',
            Formatter::CFG_AUTOMATIC_REPOSITORY  => false,
            Formatter::CFG_SKIP_GETTER_SETTER    => false,
            Formatter::CFG_ENHANCE_M2M_DETECTION => true,
            Formatter::CFG_QUOTE_IDENTIFIER      => true,
            Formatter::CFG_BACKUP_FILE           => false,
            Formatter::CFG_ANNOTATION_PREFIX     => '',
            Formatter::CFG_BASE_ENTITIES         => true,
            Formatter::CFG_TABLES_RESTRICT       => $this->tablesRestrict,
        );

        try {
            // lets stop the time
            $start = microtime(true);

            $bootstrap       = new Bootstrap();
            $this->formatter = new Formatter();
            $this->formatter->setup($setup);
            $document     = $bootstrap->export($this->formatter, $this->workbenchFile, $this->outputPath, 'file');
            $this->writer = $bootstrap->getWriter('default');

            $bootstrap->generateDerived($this->formatter, $document, dirname($this->outputPath), $this);

            // show the time needed to parse the mwb file
            $end = microtime(true);

            $this->info(sprintf("Entities generated"));
            $this->info(sprintf("Memory usage: %0.3f MB", (memory_get_peak_usage(true) / 1024 / 1024)));
            $this->info(sprintf("Time: %0.5f seconds", $end - $start));

            return $document;
        } catch (Exception $e) {
            $this->error($e->getMessage());
            $this->error($e->getTraceAsString());
            return null;
        }
    }

    private function getPath($path, $returnIfNotExists = false)
    {
        $path = getcwd() . DIRECTORY_SEPARATOR . $path;
        if (!$returnIfNotExists) {
            return realpath($path);
        }

        return $path;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('mwb-file', InputArgument::REQUIRED, 'MySQL Workbench file'),
            array('output-path', InputArgument::REQUIRED, 'Output path for generated entities'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('config-file', null, InputOption::VALUE_OPTIONAL, 'MySQL Workbench Exporter config file', null),
            array('generate-derived', 'g', InputOption::VALUE_OPTIONAL, 'Generate derived entity classes'),
            array('tables', null, InputOption::VALUE_OPTIONAL, 'A list of tables to restrict generation to using'),
        );
    }
}