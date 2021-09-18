<?php

namespace App\Console\Commands;

use App\Jobs\SendMail;
use App\Mail\ItemOrder;
use App\Services\ItemOrderServices;
use Illuminate\Console\Command;

class ImportDataOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'item-order:generate-csv {--with-mail} {mails*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate csv file from remote jsonline file';

    /**
     * Item order service
     *
     * @var mixed
     */
    protected $itemOrderService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ItemOrderServices $itemOrderService)
    {
        parent::__construct();

        $this->itemOrderService = $itemOrderService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $withMail = $this->option('with-mail');
        $csvFile = $this->itemOrderService->generateCsv();

        $this->info('Your csv has been saved in ' . $csvFile);

        if ($withMail) {
            $userMails = $this->argument('mails');

            if (count($userMails) < 1) {
                $this->info("Your csv has been saved but not sent to email because you didn't include the email address");
            } else {
                SendMail::dispatch(new ItemOrder($csvFile), $userMails);

                $this->info('The csv file has been sent to the emails');
            }
        }

        return 0;
    }
}
