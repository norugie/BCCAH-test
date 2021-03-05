<?php

    class DB
    {
        private $dbHost;
        private $dbName;
        private $dbUser;
        private $dbPass;

        protected $con;

        function setDB($host, $dbname, $user, $pass)
        {
            $this->dbHost = $host;
            $this->dbName = $dbname;
            $this->dbUser = $user;
            $this->dbPass = $pass;
        }

        function connectDB()
        {
            $info = 'mysql:host='.$this->dbHost.';dbname='.$this->dbName;
            try
            {
                $this->con = new PDO($info, $this->dbUser, $this->dbPass);
            }
            catch(PDOException $e)
            {
                print "Error Found: ".$e->getMessage().PHP_EOL;
                die();
            }

            return $this->con;
        }

        public function resultsBuildingsTable ($host, $dbname, $user, $pass, $page)
        {
            $this->setDB($host, $dbname, $user, $pass);
            $con = $this->connectDB();
        

            switch ($page) {
                case "unfiltered":
                    $query = "SELECT * FROM buildingstable";
                    break;
                case "duplicates":
                    $query = "SELECT bt.*  
                            FROM buildingstable 
                            AS bt 
                            INNER JOIN( 
                                SELECT * 
                                FROM buildingstable
                                WHERE strata_no != ''
                                GROUP BY strata_no, lat, lang
                                HAVING COUNT(strata_no) > 1 
                                AND COUNT(lat) > 1 
                                AND COUNT(lang) > 1
                            ) temp 
                            ON bt.strata_no = temp.strata_no
                            WHERE bt.strata_no != ''
                            ORDER BY bt.strata_no DESC";
                    break;
                case "merged-duplicates":
                    $query = "SELECT bt.*  
                            FROM buildingstable 
                            AS bt 
                            INNER JOIN( 
                                SELECT * 
                                FROM buildingstable
                                WHERE strata_no != ''
                                GROUP BY strata_no, lat, lang
                                HAVING COUNT(strata_no) > 1 
                                AND COUNT(lat) > 1 
                                AND COUNT(lang) > 1
                            ) temp 
                            ON bt.strata_no = temp.strata_no
                            WHERE bt.strata_no != ''
                            GROUP BY bt.name, bt.suites, bt.slug
                            ORDER BY bt.strata_no DESC";
                    break;
                default:
                    $query = "SELECT bt.*  
                            FROM buildingstable 
                            AS bt 
                            INNER JOIN( 
                                SELECT * 
                                FROM buildingstable
                                GROUP BY strata_no, lat, lang
                            ) temp 
                            ON bt.strata_no = temp.strata_no
                            GROUP BY bt.name, bt.suites, bt.slug";
                    break;
            }
                    
            $data = $con->query($query);
            $results = $data->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        }

    }

?>