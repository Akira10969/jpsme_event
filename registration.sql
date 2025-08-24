CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    competition_type VARCHAR(50) NOT NULL,
    university VARCHAR(255) NOT NULL,
    proof_natcon VARCHAR(255) NOT NULL,
    team_members TEXT NOT NULL,
    proof_enrollment TEXT NOT NULL,
    coach_name VARCHAR(255) NOT NULL,
    prc_license VARCHAR(100) NOT NULL,
    prc_reg_date DATE NOT NULL,
    prc_exp_date DATE NOT NULL,
    proof_payment VARCHAR(255) NOT NULL,
    payment_reference VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
