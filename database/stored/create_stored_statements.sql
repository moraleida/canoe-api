CREATE FUNCTION
    IF NOT EXISTS # needs mysql >= 8.0.29
    duplicate_fund_check(fund_name varchar(255), manager integer)
    RETURNS integer
    READS SQL DATA
BEGIN
    DECLARE num integer;
    SELECT COUNT(*)
    INTO num
    FROM funds f
             LEFT JOIN fund_aliases fa ON f.id = fa.fund_id
    WHERE f.fund_manager_id = manager
      AND (f.name = fund_name OR fa.name = fund_name);

    RETURN num;
END;

CREATE TRIGGER duplicate_fund_check_trigger
    BEFORE INSERT
    ON funds
    FOR EACH ROW
BEGIN
    IF duplicate_fund_check(new.name, new.fund_manager_id) > 0 THEN
        SIGNAL SQLSTATE '02222' SET MESSAGE_TEXT = 'A fund with the same name or alias and fund manager already exists';
    END IF;
END;
