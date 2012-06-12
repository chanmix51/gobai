--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: gobai; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA gobai;


SET search_path = gobai, pg_catalog;

--
-- Name: before_insert(); Type: FUNCTION; Schema: gobai; Owner: -
--

CREATE FUNCTION before_insert() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
NEW.reference := generate_reference(NEW.tags);
NEW.slug      := slugify(NEW.name);

RETURN NEW;
END;
$$;


--
-- Name: check_mandatory_exist(public.ltree); Type: FUNCTION; Schema: gobai; Owner: -
--

CREATE FUNCTION check_mandatory_exist(public.ltree) RETURNS boolean
    LANGUAGE sql
    AS $_$
SELECT $1 <@ ANY(array_agg(mt.name)) FROM mandatory_tag mt;
$_$;


--
-- Name: check_mandatory_tags(public.ltree[]); Type: FUNCTION; Schema: gobai; Owner: -
--

CREATE FUNCTION check_mandatory_tags(public.ltree[]) RETURNS boolean
    LANGUAGE sql
    AS $_$
SELECT bool_and(name @> ANY($1)) FROM mandatory_tag WHERE is_mandatory
$_$;


--
-- Name: generate_reference(public.ltree[]); Type: FUNCTION; Schema: gobai; Owner: -
--

CREATE FUNCTION generate_reference(public.ltree[]) RETURNS character varying
    LANGUAGE sql
    AS $_$
WITH
  tags (name) AS (SELECT unnest($1)),
  prio_tags (name, prio) AS (
    SELECT
      t.name,
      mt.priority
    FROM
      tags t,
      mandatory_tag mt
    WHERE
      t.name <@ mt.name
    ORDER BY priority ASC
  )
SELECT string_agg(reference, '') FROM tag NATURAL JOIN prio_tags
$_$;


--
-- Name: tag_exist(public.ltree[]); Type: FUNCTION; Schema: gobai; Owner: -
--

CREATE FUNCTION tag_exist(public.ltree[]) RETURNS boolean
    LANGUAGE sql
    AS $_$
SELECT bool_and(EXISTS (SELECT name FROM tag t WHERE t.name = gt.name)) FROM (SELECT unnest($1)) gt (name)
$_$;


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: composed_product; Type: TABLE; Schema: gobai; Owner: -; Tablespace: 
--

CREATE TABLE composed_product (
    reference character varying NOT NULL,
    composed_of character varying NOT NULL
);


--
-- Name: mandatory_tag; Type: TABLE; Schema: gobai; Owner: -; Tablespace: 
--

CREATE TABLE mandatory_tag (
    name public.ltree NOT NULL,
    is_mandatory boolean DEFAULT false,
    priority smallint
);


--
-- Name: product; Type: TABLE; Schema: gobai; Owner: -; Tablespace: 
--

CREATE TABLE product (
    reference character varying NOT NULL,
    name character varying NOT NULL,
    description text NOT NULL,
    tags public.ltree[] NOT NULL,
    price numeric(6,2),
    slug character varying NOT NULL,
    CONSTRAINT check_mandatory_tags CHECK (check_mandatory_tags(tags)),
    CONSTRAINT check_tags_exist CHECK (tag_exist(tags)),
    CONSTRAINT product_price_check CHECK ((COALESCE(price, (0)::numeric) >= (0)::numeric))
);


--
-- Name: tag; Type: TABLE; Schema: gobai; Owner: -; Tablespace: 
--

CREATE TABLE tag (
    reference character(2) NOT NULL,
    name public.ltree NOT NULL,
    CONSTRAINT check_mandatory_exist CHECK (check_mandatory_exist(name))
);


--
-- Name: composed_product_pkey; Type: CONSTRAINT; Schema: gobai; Owner: -; Tablespace: 
--

ALTER TABLE ONLY composed_product
    ADD CONSTRAINT composed_product_pkey PRIMARY KEY (reference, composed_of);


--
-- Name: mandatory_tag_pkey; Type: CONSTRAINT; Schema: gobai; Owner: -; Tablespace: 
--

ALTER TABLE ONLY mandatory_tag
    ADD CONSTRAINT mandatory_tag_pkey PRIMARY KEY (name);


--
-- Name: product_pkey; Type: CONSTRAINT; Schema: gobai; Owner: -; Tablespace: 
--

ALTER TABLE ONLY product
    ADD CONSTRAINT product_pkey PRIMARY KEY (reference);


--
-- Name: tag_pkey; Type: CONSTRAINT; Schema: gobai; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tag
    ADD CONSTRAINT tag_pkey PRIMARY KEY (reference);


--
-- Name: unique_name; Type: CONSTRAINT; Schema: gobai; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tag
    ADD CONSTRAINT unique_name UNIQUE (name);


--
-- Name: unique_slug; Type: CONSTRAINT; Schema: gobai; Owner: -; Tablespace: 
--

ALTER TABLE ONLY product
    ADD CONSTRAINT unique_slug UNIQUE (slug);


--
-- Name: product_before_insert_trig; Type: TRIGGER; Schema: gobai; Owner: -
--

CREATE TRIGGER product_before_insert_trig BEFORE INSERT ON product FOR EACH ROW EXECUTE PROCEDURE before_insert();


--
-- Name: composed_of_fk; Type: FK CONSTRAINT; Schema: gobai; Owner: -
--

ALTER TABLE ONLY composed_product
    ADD CONSTRAINT composed_of_fk FOREIGN KEY (composed_of) REFERENCES product(reference);


--
-- Name: product_fk; Type: FK CONSTRAINT; Schema: gobai; Owner: -
--

ALTER TABLE ONLY composed_product
    ADD CONSTRAINT product_fk FOREIGN KEY (reference) REFERENCES product(reference);


--
-- PostgreSQL database dump complete
--

