# Major features

Loads objects from database by id with multilayer caching.

Stores objects to database and deletes objects by id. Supports pre- and post-update callbacks and automatic cache invalidation. 

Provides CLI interface for model class creation, which also generates database migration. 

Provides CLI interface for adding new fields to model, which also generates database migrations, with foreign keys support and selector methods generation.   

# Code examples

Creating new model and storing it to database:

    $new_model = new TestModel();
    $new_model->title = rand(1, 1000);
    $new_model->save();
    
After save() the model will has the id value, generated by database.

Loading model from database by id:

    $model_obj = TestModel::factory(1);
    
Model class looks like:
    
    class DemoModel implements ActiveRecordInterface
    {
        use ActiveRecordTrait;
        use ProtectPropertiesTrait;
    
        const DB_ID = 'phpmodel';
        const DB_TABLE_NAME = 'phpmodeldemo_demomodel';
    
        const _CREATED_AT_TS = 'created_at_ts';
        public $created_at_ts; // initialized by constructor
        const _TITLE = 'title';
        public $title;
        const _ID = 'id';
        protected $id;
        
        public function __construct(){
            $this->created_at_ts = time();
        }
    
        public function getId()
        {
            return $this->id;
        }
    
        static public function idsByCreatedAtDesc($offset = 0, $page_size = 30){
            $ids_arr = \OLOG\DB\DB::readColumn(
                self::DB_ID,
                'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME . ' order by ' . self::_CREATED_AT_TS . ' desc limit ? offset ?',
                [$page_size, $offset]
            );
            return $ids_arr;
        }
    }

Class above has one selector method idsByCreatedAtDesc(): it returns array of model ids, sorted by creation date.

Selectors concept is important part of the library. Separate selector functions allow to:

- profile and fine-tune every database query separately, which is critically important for heavily loaded applications
- have single point of caching the query result and of invalidating the cache

Selectors return only ids, not objects. Thus every model is cached only once in the model cache and the selections cache remains lightweigh. Also, the data exchange with database and within application is reduced to required minimum.   

# Installation for contributors

Example below requires Linux, MacOS or Windows with Linux Subsystem and installed php, mysql and composer.

    git clone https://github.com/o-log/php-model.git
    cd php-model
    composer update

Now open Config/Config.php file and find the following line:

    DBConfig::setConnector(self::CONNECTOR_PHPMODELDEMO, new ConnectorMySQL('127.0.0.1', 'phpmodel', 'root', '1234'));

You have to replace '1234' with your local mysql root user password. Also you have to create empty "phpmodel" database.

Now you are ready to execute migrations and see the demo page:

    vendor/bin/migrate
    bin/run

Open localhost:8000 in your browser.

# ActiveRecordTrait

Trait helps load objects from database and save them. 

load() method:

- reads database record by id and stores every record field to the object properties with the same name

- throws exception if given id is not found (exception can be suppressed using second factory parameter)  

save() method: 

- if the object has non-empty id value - update database record with this id value. Every object property is stored to the record field with the same name.

- if the object id value is empty (new object) - inserts new record to database table. Object properties are stored to record fields. 

To work with ActiveRecordTrait class must:

- has the id field

- has the DB_TABLE_NAME constant, which value is the name of the database table

- has the DB_ID constant, which value is the name of php-db tablespace (see php-db docs)

- class properties must match the table columns (however properties may be protected)

## Extending the load, save and delete operations

You can perform extra operations before saving and after saving the model by defining beforeSave() and afterSave() methods:

    public function beforeSave(){
        $this->setBody($this->getTitle() . $this->getTitle());
    }


beforeSave() method can alter model data before saving or block saving by throwing the exception.

    public function afterSave()
    {
        $this->removeFromFactoryCache();

        $term_to_node_ids_arr = DemoTermToNode::idsForNodeIdByCreatedAtDesc($this->getId());
        foreach ($term_to_node_ids_arr as $term_to_node_id){
            $term_to_node_obj = DemoTermToNode::factory($term_to_node_id);
            $term_to_node_obj->setCreatedAtTs($this->getCreatedAtTs());
            $term_to_node_obj->save();
        }
    }

afterSave() method can reset caches or update dependent models. 

The same pair of methods can be defined for model deletion: canDelete() and afterDelete():

    public function canDelete(&$message){
        if ($this->getDisableDelete()){
            $message = 'Delete disabled';
            return false;
        }

        return true;
    }

    public function afterDelete(){
        $this->removeFromFactoryCache();
        
        $match_obj = Match::factory($this->getMatchId());

        // update game title after removing teams from match
        $match_obj->regenerateTitle();
        $match_obj->save();
    }

afterSave() and afterDelete() methods has the default implementation, which resets the model cache. You have to call $this->removeFromFactoryCache() in your overriden methods.   
It is recommended to reset in the beginning of the method - thus any following code will see the new version of the object.

## Transactions

Save and delete operations are performed within transactions, thus you can safely throw exceptions inside beforeSave(), afterSave(), canDelete() и afterDelete() - the database will be restored to the state before save or delete. 

# Creating new model class

To create new model class run vendor/bin/model and choose "Create new model" in the menu, then answer a few questions. The tool will create php class file and database migrations. 

# Adding new fields to the model

To add new field to existing model run vendor/bin/model and choose "Add model field".

The tool wil ask for required data and alter the model class and generate database migration. 

Also the tool can create the selector method for new field, add unique or foreign key.   

# Concepts

The library has appeared while developing several large applications to solve the following problems:

- quick training of new developers 
- speed-up and simplify development and support of the applications
- maximum performance: reduce overhead and simplify optimization  

# Tests

Run bin/tests to execute tests.

# ProtectPropertiesTrait

Throws exception when trying to access properties, not declared in the class.
