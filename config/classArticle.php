<?php

    require_once("parsedown/Parsedown.php");

    class Article
    {

        public ?int     $id                 = NULL;     // ID artykułu w bazie danych
        public ?string  $title              = NULL;     // tytuł
        public ?string  $summary            = NULL;     // krótkie wprowadzenie
        public ?string  $publicationDate    = NULL;     // data opublikowanie
        public ?string  $content            = NULL;     // rozwinięcie
        public ?string  $author             = NULL;     // nazwa autora
        public ?string  $thumbnail          = NULL;     // nazwa miniaturki (przez link)
        public ?int     $published          = NULL;     // widoczność

        public function __construct(array $data = [])
        {
            if (isset($data['id'])) $this->id = (int) $data['id'];
            if (isset($data['publication_date'])) $this->publicationDate = $data['publication_date'];
            if (isset($data['title'])) $this->title = $data['title'];
            if (isset($data['summary'])) $this->summary = $data['summary'];
            if (isset($data['content'])) $this->content = $data['content'];
            if (isset($data['author'])) $this->author = $data['author'];
            if (isset($data['thumbnail'])) $this->thumbnail = $data['thumbnail'];
            if (isset($data['published'])) $this->published = $data['published'];
        }

        public function storePostData(array $postData): self
        {
            $this->__construct($postData);
            return $this;
        }

        public static function getById(int $id): self
        {
            $link = dbConnect();
            $stmt = $link->prepare(
                "SELECT article.id, article.publication_date, article.title, article.summary, article.content, article.thumbnail, users.username AS author 
                FROM article, users 
                WHERE users.id=article.author AND article.id=?"
            );
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $link->close();

            if (is_null($res)) throw new Exception;

            return new self($res);
        }

        public static function getList(int $numRows = 1000000, int $offset = 0): array
        {
            $link = dbConnect();

            $stmt = $link->prepare(
                "SELECT article.id, article.publication_date, article.title, article.summary, article.content, article.thumbnail, users.username AS author 
                FROM article, users 
                WHERE users.id=article.author AND article.published=1 
                ORDER BY article.publication_date DESC 
                LIMIT ? OFFSET ?"
            );

            $stmt->bind_param('ii', $numRows, $offset);
            $stmt->execute();
            $res = $stmt->get_result();
            $stmt->close();
            $link->close();

            $list = [];
            while ($row = $res->fetch_assoc()) {
                $list[] = new self($row);
            }

            return $list;
        }
        
        public static function getByOwner(int $owner): array
        {
            $link = dbConnect();

            $stmt = $link->prepare(
                "SELECT article.id, article.publication_date, article.title, article.summary, article.content, article.thumbnail, article.published, users.username AS author 
                FROM article, users 
                WHERE users.id=article.author AND article.author=?
                ORDER BY article.publication_date DESC"
            );

            $stmt->bind_param('i', $owner);
            $stmt->execute();
            $res = $stmt->get_result();
            $stmt->close();
            $link->close();

            $list = [];
            while ($row = $res->fetch_assoc()) {
                $list[] = new self($row);
            }

            return $list;
        }

        public function insert(int $author_id, int $published=0): self
        {
            if (!is_null($this->id)) trigger_error("Article::insert(): Attempt to insert an Article object with its ID previously set.",E_USER_ERROR);
            $this->publicationDate = date('Y-m-d');

            $link = dbConnect();
            $stmt = $link->prepare(
                "INSERT INTO article (title, summary, content, publication_date, author, thumbnail, published) 
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                'sssssii', 
                $this->title, 
                $this->summary, 
                $this->content, 
                $this->publicationDate,
                $author_id,
                $this->thumbnail,
                $published
            );
            
            $stmt->execute();
            $this->id = $stmt->insert_id;
            $stmt->close();
            $link->close();

            return $this;
        }

        public function update(): self
        {
            if (is_null($this->id)) trigger_error("Article::update(): Attempt to update an Article object with its ID not set.",E_USER_ERROR);

            $link = dbConnect();
            $stmt = $link->prepare(
                "UPDATE article
                SET title=?, summary=?, content=?, publication_date=?, thumbnail=?, published=?
                WHERE id=?"
            );
            $stmt->bind_param(
                'sssssii', 
                $this->title, 
                $this->summary, 
                $this->content, 
                $this->publicationDate, 
                $this->thumbnail,
                $this->published,
                $this->id
            );

            $stmt->execute();
            $stmt->close();
            $link->close();

            return $this;
        }

        public function delete(): void
        {
            if (is_null($this->id)) trigger_error("Article::delete(): Attempt to delete an Article object with its ID not set.",E_USER_ERROR);

            $link = dbConnect();
            $stmt = $link->prepare("DELETE FROM article WHERE id=? LIMIT 1");
            $stmt->bind_param('i', $this->id);
            $stmt->execute();
            $stmt->close();
            $link->close();
        }

        public function publish(?int $author_id=NULL): self
        {
            $this->published = 1;
            if (is_null($this->id)) $this->insert($author_id, 1);
            else $this->update();
            return $this;
        }

        public function parse(): self
        {
            $pd = new Parsedown;
            $parsed = $pd->text($this->summary);
            $this->summary = !preg_match("/<p>.*<\/p>/",$parsed) ? $this->summary = "<p>{$parsed}</p>" : $parsed;
            $parsed = $pd->text($this->content);
            $this->content = !preg_match("/<p>.*<\/p>/",$parsed) ? $this->content = "<p>{$parsed}</p>" : $parsed;
            return $this;
        }
    }