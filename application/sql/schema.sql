--
-- PostgreSQL database dump
--

-- Dumped from database version 9.5.3
-- Dumped by pg_dump version 12.2

-- Started on 2020-10-24 02:42:41

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 6 (class 2615 OID 2200)
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO postgres;

--
-- TOC entry 2113 (class 0 OID 0)
-- Dependencies: 6
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'standard public schema';


SET default_tablespace = '';

--
-- TOC entry 182 (class 1259 OID 16937)
-- Name: fiddles; Type: TABLE; Schema: public; Owner: pg_user
--

CREATE TABLE public.fiddles (
    id integer NOT NULL,
    points json,
    title text,
    ctime timestamp without time zone DEFAULT timezone('utc'::text, now()),
    mtime timestamp without time zone DEFAULT timezone('utc'::text, now()),
    "forkFrom" text,
    key text,
    ip text,
    version text,
    type bigint DEFAULT '0'::bigint
);


ALTER TABLE public.fiddles OWNER TO pg_user;

--
-- TOC entry 181 (class 1259 OID 16935)
-- Name: fiddles_id_seq; Type: SEQUENCE; Schema: public; Owner: pg_user
--

CREATE SEQUENCE public.fiddles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.fiddles_id_seq OWNER TO pg_user;

--
-- TOC entry 2115 (class 0 OID 0)
-- Dependencies: 181
-- Name: fiddles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: pg_user
--

ALTER SEQUENCE public.fiddles_id_seq OWNED BY public.fiddles.id;


--
-- TOC entry 1986 (class 2604 OID 16940)
-- Name: fiddles id; Type: DEFAULT; Schema: public; Owner: pg_user
--

ALTER TABLE ONLY public.fiddles ALTER COLUMN id SET DEFAULT nextval('public.fiddles_id_seq'::regclass);

SELECT pg_catalog.setval('public.fiddles_id_seq', 1, true);


--
-- TOC entry 1991 (class 2606 OID 16947)
-- Name: fiddles fiddles_pkey; Type: CONSTRAINT; Schema: public; Owner: pg_user
--

ALTER TABLE ONLY public.fiddles
    ADD CONSTRAINT fiddles_pkey PRIMARY KEY (id);


--
-- TOC entry 2114 (class 0 OID 0)
-- Dependencies: 6
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2020-10-24 02:42:49

--
-- PostgreSQL database dump complete
--

