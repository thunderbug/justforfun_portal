<?php

namespace JustForFun\Portal;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;

use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Setup extends AbstractSetup
{
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;

	public function installStep1()
    {
        $this->schemaManager()->createTable("xf_jff_log_restart", function (Create $table) {
            $table->addColumn("action_id", "int")->autoIncrement();
            $table->addColumn("user_id", "int");
            $table->addColumn("server_id", "int");
            $table->addColumn("date" , "int");
            $table->addColumn("action" , "varchar", "10");
            $table->addPrimaryKey("action_id");
        });
    }

    public function installStep2()
    {
        $this->schemaManager()->createTable("xf_jff_cache_servers", function (Create $table) {
           $table->addColumn("server_id", "int")->autoIncrement();
           $table->addColumn("ip", "varchar", 255);
           $table->addColumn("data", "text");
           $table->addColumn("date", "int");
           $table->addPrimaryKey("server_id");
        });
    }

    public function upgrade1000100Step1()
    {
	    //nothing to do yet
    }

    public function upgrade1000200Step1()
    {
        $this->schemaManager()->dropTable("xf_jff_log_restart");

        $this->schemaManager()->createTable("xf_jff_log_restart", function (Create $table) {
            $table->addColumn("action_id", "int")->autoIncrement();
            $table->addColumn("user_id", "int");
            $table->addColumn("server_id", "int");
            $table->addColumn("date" , "int");
            $table->addColumn("action" , "varchar", "10");
            $table->addPrimaryKey("action_id");
        });
    }

    public function uninstallStep1()
    {
        $this->schemaManager()->dropTable("xf_jff_log_restart");
    }

    public function uninstallStep2()
    {
        $this->schemaManager()->dropTable("xf_jff_cache_servers");
    }
}