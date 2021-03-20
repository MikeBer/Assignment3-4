<?php

$dbconn = pg_pconnect("host=$pg_host port=$pg_port dbname=$pg_dbname user=$pg_dbuser password=$pg_dbpassword") or die("Could not connect");
if ($debug) {
	echo "host=$pg_host, port=$pg_port, dbname=$pg_dbname, user=$pg_dbuser, password=$pg_dbpassword<br>";
	$stat = pg_connection_status($dbconn);
	if ($stat === PGSQL_CONNECTION_OK) {
		echo 'Connection status ok';
	} else {
		echo 'Connection status bad';
	}    
}

//database functions
function get_article_list($dbconn){
	$query=
		"SELECT 
		articles.created_on as date,
		articles.aid as aid,
		articles.title as title,
		authors.username as author,
		articles.stub as stub
		FROM
		articles
		INNER JOIN
		authors ON articles.author=authors.id
		ORDER BY
		date DESC";
    $result=pg_query($dbconn, $query);
    if ($debug) {
		echo "$query<br>";
	}
	if ($result == False and $debug) {
		echo "Query failed<br>";
	}
	return $result;
}

function get_article($dbconn, $aid) {
	$statement= 
		"SELECT 
		articles.created_on as date,
		articles.aid as aid,
		articles.title as title,
		authors.username as author,
		articles.stub as stub,
		articles.content as content
		FROM 
		articles
		INNER JOIN
		authors ON articles.author=authors.id
		WHERE
		aid=$1
		LIMIT 1";
    $result=pg_prepare($dbconn, "", $statement);
    $result=pg_execute($dbconn, "", array($aid)); 
    if ($debug) {
		echo "$query<br>";
	}
	if ($result == False and $debug) {
		echo "Query failed<br>";
	}
	return $result;
}

function delete_article($dbconn, $aid) {
	$statement= "DELETE FROM articles WHERE aid=$1";
	$result=pg_prepare($dbconn, "", $statement);
        $result=pg_execute($dbconn, "", array($aid)); 
        if ($debug) {
		echo "$query<br>";
	}
	if ($result == False and $debug) {
		echo "Query failed<br>";
	}
	return $result;
}

function add_article($dbconn, $title, $content, $author) {
	$stub = substr($content, 0, 30);
	$aid = str_replace(" ", "-", strtolower($title));
	$statement="
		INSERT INTO
		articles
		(aid, title, author, stub, content) 
		VALUES
		($1, $2, $3, $4, $5)";
	 $result=pg_prepare($dbconn,"", $statement);
    $result=pg_execute($dbconn, "", array($aid, $title, $author, $stub, $content)); 
    if ($debug) {
		echo "$query<br>";
	}
	if ($result == False and $debug) {
		echo "Query failed<br>";
	}
	return $result;
}

function update_article($dbconn, $title, $content, $aid) {
	$statement=
		"UPDATE articles
		SET 
		title=$1,
		content=$2
		WHERE
		aid=$3";
    $result=pg_prepare($dbconn, "", $statement);
    $result=pg_execute($dbconn, "", array($title, $content, $aid));
	if ($debug) {
		echo "$query<br>";
	}
	if ($result == False and $debug) {
		echo "Query failed<br>";
	}
	return $result;
}

function authenticate_user($dbconn, $username, $password) {
    $statement='SELECT
        authors.id as id,
        authors.username as username,
        authors.password as password,
        authors.role as role
        FROM
        authors
        WHERE
        username=$1
        AND
        password=$2
        LIMIT 1';
    $result=pg_prepare($dbconn,"", $statement);
    $result=pg_execute($dbconn, "", array($username, $password)); 
    if ($debug) {
		echo "$query<br>";
	}
	if ($result == False and $debug) {
		echo "Query failed<br>";
	}
	return $result;
}	
?>
