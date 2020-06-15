
## This is Idenary submission for the Idena hackathon

https://Idenary.com is a fresh game and art project built upon Idena.  
We compete for the "sign in with Idena" contest. Feel free to start and watch this repo to show your support!

Idena official website is to be found at https://idena.io/

## Benefits for Idena

We hope our little experiment will show that an identity centric solution like Idena is not limited to serious issues like voting and governance.  
Our own sensitivity - our weaknesses - what we chose to hide or to show are integral part of our humanity, and have their own space to find in such tools.

Using Idena as scaffold for an expression media then gives it all its meaning.  
Idena was built by humans, for humans. What is more human than art?

Being free and - we hope - increasingly fun to use, Idenary will likely be a welcomed counterweight to all the "down to earth" technical gimmick using crypto identities.  
As a result: more adoption, growing user base and regular media interest.

One last thing: for idenary, we developped our own Idena authentifier class in PHP.  
This likely will be polished a bit and published as a composer module later on.  
Meaning: even if you don't like our art work you still can reuse our simple auth module in any php website - no need for third part server, database or complex setup!.
PHP being so popular, this alone we feel is a nice gift ;)


## Installation

All required code is being published.  
Complete installation doc still is WIP but should not be an issue if you know some PHP.  
We can be of help if you have precise questions.

Website itself and related code is subject to major changes. Don't expect this repo nor the website to be in a stable state at any point in time.  
After all, we're artists more than machines :)

See composer.json for required modules.

### cache

cache directory needs the proper permissions. session tokens are stored there.

### config

rename `inc/config.inc.php-default` to `inc/config.inc.php` and edit.

### websocket server

Directory wsserver, ratchet based websocket server. Run with php server.php (this one does not run under apache)

### sql database

Needs a mysql database, see config for access info.

Mysql structure is the following:

```

--
-- Table structure for table `address_bundle`
--

CREATE TABLE `address_bundle` (
  `address` varchar(100) NOT NULL,
  `bundle_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bundles`
--

CREATE TABLE `bundles` (
  `id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `data` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bundle_details`
--

CREATE TABLE `bundle_details` (
  `bundle_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `credits`
--

CREATE TABLE `credits` (
  `status` varchar(20) NOT NULL,
  `credits` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `squares`
--

CREATE TABLE `squares` (
  `id` int(11) NOT NULL,
  `address` varchar(100) NOT NULL DEFAULT '',
  `item` varchar(100) NOT NULL DEFAULT '',
  `rotate` tinyint(4) NOT NULL DEFAULT '0',
  `color` varchar(6) NOT NULL DEFAULT '000',
  `bgcolor` varchar(6) NOT NULL DEFAULT 'FFFFFF',
  `started` bigint(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--


--
-- Indexes for table `address_bundle`
--
ALTER TABLE `address_bundle`
  ADD PRIMARY KEY (`address`,`bundle_id`);

--
-- Indexes for table `bundles`
--
ALTER TABLE `bundles`
  ADD PRIMARY KEY (`id`,`type`,`data`) USING BTREE;

--
-- Indexes for table `bundle_details`
--
ALTER TABLE `bundle_details`
  ADD PRIMARY KEY (`bundle_id`);

--
-- Indexes for table `credits`
--
ALTER TABLE `credits`
  ADD PRIMARY KEY (`status`);

--
-- Indexes for table `squares`
--
ALTER TABLE `squares`
  ADD PRIMARY KEY (`id`);
```

"Credits" table holds the number of credits for every Idena Identity state:

```
--
-- Dumping data for table `credits`
--

INSERT INTO `credits` (`status`, `credits`) VALUES
('', 0),
('Candidate', 0),
('Human', 27),
('Newbie', 7),
('Suspended', 9),
('Verified', 18),
('Zombie', 9);
```

## Contact

- Twitter @idenary_com https://twitter.com/idenary_com  
- Discord https://discord.gg/GAbu57d


## Donation addresses

A tip, be it large or small, always is appreciated!

DNA `0xcb433bdcf16510935a7dedbdefa9a2254cf61b25`  
ETH `0x12bf3bfbA5D34c36D6F48aafB64c26F30e59d02A`

## Open source

This project source code is released under a very permissive GPL 2-clause licence.  
Please do not abuse it and keep the credits!
