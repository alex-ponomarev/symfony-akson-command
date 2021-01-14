<?php


namespace App\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\FileManager\AksonFileManager;

class UploadFileCommand extends Command
{
    /**
     * @var AksonFileManager
     */
    private AksonFileManager $afl;

    public function __construct(AksonFileManager $afl)
    {
        // хорошей практикой считается вызывать сначала родительский конструктор и
        // потом установка своих свойств. Это не сработает в данном случае
        // потому что configure() нуждается в установленных свойствах в конструкторе
        $this->afl = $afl;

        parent::__construct();
    }
    protected function configure()
    {
        $this
            // имя команды (часть после "bin/console")
            ->setName('app:upload-akson-file')

            // краткое описание, отображающееся при запуске "php bin/console list"
            ->setDescription('Загрузить файл Аксона в БД и ElasticSearch')

            // полное описание команды, отображающееся при запуске команды
            // с опцией "--help"
            ->setHelp('Загрузить файл Аксона в БД и Эластик')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Началась обработка аксон-файла: products.txt');
        $this->afl->startProcessFile();
        $output->writeln('Обработка завершена. Данные добавлены в БД и синхронизированы с ElasticSearch');
        return 1;
    }
}