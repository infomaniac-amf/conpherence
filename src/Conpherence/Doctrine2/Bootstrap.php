<?php
namespace Conpherence\Doctrine2;

use Conpherence\Doctrine2\Formatter;
use MwbExporter\Bootstrap as BaseBootstrap;
use MwbExporter\Formatter\FormatterInterface;
use MwbExporter\Logger\Logger;
use MwbExporter\Logger\LoggerConsole;
use MwbExporter\Logger\LoggerFile;
use MwbExporter\Logger\LoggerInterface;
use MwbExporter\Model\Catalog;
use MwbExporter\Model\Document;
use MwbExporter\Model\Table;
use MwbExporter\Storage\LoggedStorage;
use MwbExporter\Writer\WriterInterface;

class Bootstrap extends BaseBootstrap
{
    /**
     * Load workbench schema and generate the code.
     *
     * @param \MwbExporter\Formatter\FormatterInterface $formatter
     * @param string $filename
     * @param string $outDir
     * @param string $storage
     * @throws \Exception
     * @return \MwbExporter\Model\Document
     */
    public function export(FormatterInterface $formatter, $filename, $outDir, $storage = 'file')
    {
        if ($formatter && $storage = $this->getStorage($storage)) {
            if ($formatter->getRegistry()->config->get(FormatterInterface::CFG_USE_LOGGED_STORAGE)) {
                $storage = new LoggedStorage($storage);
            }
            $storage->setOutdir(realpath($outDir) ? realpath($outDir) : $outDir);
            $storage->setBackup($formatter->getRegistry()->config->get(FormatterInterface::CFG_BACKUP_FILE));
            $writer = $this->getWriter($formatter->getPreferredWriter());
            $writer->setStorage($storage);
            $document = new Document($formatter, $filename);
            if (strlen($logFile = $formatter->getRegistry()->config->get(FormatterInterface::CFG_LOG_FILE))) {
                $logger = new LoggerFile(array('filename' => $logFile));
            } elseif ($formatter->getRegistry()->config->get(FormatterInterface::CFG_LOG_TO_CONSOLE)) {
                $logger = new LoggerConsole();
            } else {
                $logger = new Logger();
            }
            $document->setLogger($logger);
            $document->write($writer);
            if ($e = $document->getError()) {
                throw $e;
            }

            return $document;
        }
    }

    public function generateDerived(FormatterInterface $formatter, Document $document, $outputPath)
    {
        // exclude m-m join tables
        $modelsToGenerate = array();
        $tablesRestrict = $document->getConfig()->get(Formatter::CFG_TABLES_RESTRICT);

        $schemas = $document->getPhysicalModel()->getCatalog()->getSchemas();
        foreach ($schemas as $schema) {
            $tables = $schema->getTables();
            if (!count($tables)) {
                continue;
            }

            foreach ($tables as $table) {
                // check if table has been restricted from being generated
                if (count($tablesRestrict) && !in_array(strtolower($table->getModelName()), $tablesRestrict)) {
                    continue;
                }

                $modelsToGenerate[] = $table;
            }
        }

        $writer = $document->getWriter();
        $logger = $document->getLogger();

        $document->getWriter()->getStorage()->setOutdir($outputPath);

        $logger->log("");
        $logger->log("Generating " . count($modelsToGenerate) . " models into " . $outputPath);
        foreach ($modelsToGenerate as $model) {
            $modelPath = $outputPath . "/" . $model->getModelName() . ".php";
            if (file_exists($modelPath)) {
                $logger->log("* Skipping $modelPath - already exists");
                continue;
            }

            $this->writeDerivedModel($modelPath, $model, $writer, $logger);
        }
    }

    public function writeDerivedModel($modelPath, Table $model, WriterInterface $writer, LoggerInterface $logger)
    {
        $writer
            ->open($model->getModelName().".php")
            ->write('<?php')
            ->write('')
            ->write("namespace %s;", $model->getEntityNamespace())
            ->write('')
            ->write('use Conpherence\Entities\Base\Base%s;', $model->getModelName())
            ->write('')
            ->write('/**')
            ->write(' * @Entity')
            ->write(' * @HasLifecycleCallbacks')
            ->write(' */')
            ->write('class %s extends Base%s', $model->getModelName(), $model->getModelName())
            ->write('{')
            ->write('}')
            ->close();

        $logger->log("* " . $model->getModelName() . (file_exists($modelPath) ? "[OK]" : "[ERROR]"));
    }
}