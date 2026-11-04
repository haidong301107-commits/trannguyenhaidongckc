-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th10 05, 2025 lúc 01:02 PM
-- Phiên bản máy phục vụ: 11.8.3-MariaDB-log
-- Phiên bản PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `quanly_sinhvien`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khoa`
--

CREATE TABLE `khoa` (
  `makhoa` varchar(20) NOT NULL,
  `tenkhoa` varchar(100) NOT NULL,
  `mota` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `khoa` (`makhoa`, `tenkhoa`, `mota`) VALUES
('CK', 'Cơ khí', 'Khoa đào tạo kỹ sư cơ khí chế tạo và tự động hóa.'),
('CNTT', 'Công nghệ thông tin', 'Khoa đào tạo lập trình, mạng và AI.'),
('DDT', 'Điện - Điện tử', 'Khoa đào tạo kỹ sư điện, điện tử, điều khiển và viễn thông.');


CREATE TABLE `lop` (
  `malop` varchar(20) NOT NULL,
  `tenlop` varchar(100) NOT NULL,
  `makhoa` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `lop` (`malop`, `tenlop`, `makhoa`) VALUES
('CK23A', 'Cơ khí 23A', 'CK'),
('CK23B', 'Cơ khí 23B', 'CK'),
('CK23C', 'Cơ khí 23C', 'CK'),
('CK24A', 'Cơ khí 24A', 'CK'),
('CK24B', 'Cơ khí 24B', 'CK'),
('CNTT23A', 'Công nghệ thông tin 23A', 'CNTT'),
('CNTT23B', 'Công nghệ thông tin 23B', 'CNTT'),
('CNTT23C', 'Công nghệ thông tin 23C', 'CNTT'),
('CNTT24A', 'Công nghệ thông tin 24A', 'CNTT'),
('CNTT24B', 'Công nghệ thông tin 24B', 'CNTT'),
('DDT23A', 'Điện - Điện tử 23A', 'DDT'),
('DDT23B', 'Điện - Điện tử 23B', 'DDT'),
('DDT23C', 'Điện - Điện tử 23C', 'DDT'),
('DDT24A', 'Điện - Điện tử 24A', 'DDT'),
('DDT24B', 'Điện - Điện tử 24B', 'DDT');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quan_tri_vien`
--

CREATE TABLE `quan_tri_vien` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `hoten` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `quan_tri_vien`
--

INSERT INTO `quan_tri_vien` (`id`, `username`, `password`, `hoten`, `email`, `last_login`) VALUES
(1, 'lhvinh', '$2y$10$cmrbljNUuAaFt2/srStwGO4JjZj9K7j9XqR8FLB5WbFc9Fi0rnc2q', 'Lê Hữu Vinh', 'lhvinh@caothang.edu.vn', '2025-11-05 12:55:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sinh_vien`
--

CREATE TABLE `sinh_vien` (
  `mssv` varchar(20) NOT NULL,
  `hoten` varchar(100) NOT NULL,
  `ngaysinh` date DEFAULT NULL,
  `gioitinh` enum('Nam','Nữ','Khác') DEFAULT 'Nam',
  `malop` varchar(20) DEFAULT NULL,
  `makhoa` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `diachi` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `trangthai` enum('Đang học','Tốt nghiệp','Bảo lưu','Đã nghỉ') DEFAULT 'Đang học'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sinh_vien`
--

INSERT INTO `sinh_vien` (`mssv`, `hoten`, `ngaysinh`, `gioitinh`, `malop`, `makhoa`, `email`, `sdt`, `diachi`, `password`, `trangthai`) VALUES
('2312401001', 'Nguyễn Văn An', '2005-03-12', 'Nam', 'CNTT23A', 'CNTT', '2312401001@ct.edu.vn', '0901234567', 'TP. Huế', '$2y$10$HS8by4lYLuFtbAFq.28SC.ASYxbHQ5fCqlxULkKekOLOy5szj/b6e', 'Đang học'),
('2312401002', 'Trần Thị Bình', '2005-06-20', 'Nữ', 'CNTT23A', 'CNTT', '2312401002@ct.edu.vn', '0902345678', 'TP. Đà Nẵng', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401003', 'Lê Văn Cường', '2004-09-10', 'Nam', 'CNTT23A', 'CNTT', '2312401003@ct.edu.vn', '0903456789', 'Quảng Trị', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401004', 'Phạm Thị Dung', '2005-02-17', 'Nữ', 'CNTT23A', 'CNTT', '2312401004@ct.edu.vn', '0904567890', 'TP. Hồ Chí Minh', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401005', 'Hoàng Anh Tuấn', '2004-12-10', 'Nam', 'CNTT23A', 'CNTT', '2312401005@ct.edu.vn', '0905678901', 'Cần Thơ', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401006', 'Nguyễn Thị Hồng', '2005-07-18', 'Nữ', 'CNTT23A', 'CNTT', '2312401006@ct.edu.vn', '0906789012', 'Huế', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401007', 'Đỗ Minh Quân', '2005-05-05', 'Nam', 'CNTT23A', 'CNTT', '2312401007@ct.edu.vn', '0907890123', 'Đà Nẵng', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401008', 'Trần Thu Hà', '2005-01-23', 'Nữ', 'CNTT23A', 'CNTT', '2312401008@ct.edu.vn', '0908901234', 'Hà Nội', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401009', 'Nguyễn Văn Dũng', '2005-03-29', 'Nam', 'CNTT23A', 'CNTT', '2312401009@ct.edu.vn', '0909012345', 'Bình Dương', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401010', 'Phạm Thị Lan', '2004-11-11', 'Nữ', 'CNTT23A', 'CNTT', '2312401010@ct.edu.vn', '0910123456', 'Huế', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401011', 'Lê Hữu Phước', '2005-05-18', 'Nam', 'CNTT23A', 'CNTT', '2312401011@ct.edu.vn', '0911234567', 'Đồng Nai', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401012', 'Trần Thị Mai', '2005-04-24', 'Nữ', 'CNTT23A', 'CNTT', '2312401012@ct.edu.vn', '0912345678', 'Huế', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401013', 'Nguyễn Thanh Tâm', '2004-10-30', 'Nam', 'CNTT23A', 'CNTT', '2312401013@ct.edu.vn', '0913456789', 'TP. HCM', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401014', 'Phan Văn Khải', '2005-02-19', 'Nam', 'CNTT23A', 'CNTT', '2312401014@ct.edu.vn', '0914567890', 'Cần Thơ', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401015', 'Đặng Thị Vân', '2004-09-22', 'Nữ', 'CNTT23A', 'CNTT', '2312401015@ct.edu.vn', '0915678901', 'Đà Nẵng', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401016', 'Võ Văn Hậu', '2005-06-13', 'Nam', 'CNTT23A', 'CNTT', '2312401016@ct.edu.vn', '0916789012', 'Huế', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401021', 'Trần Thị Kim', '2005-04-19', 'Nữ', 'CNTT23B', 'CNTT', '2312401021@ct.edu.vn', '0921234567', 'Huế', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401022', 'Đoàn Minh Khang', '2005-06-03', 'Nam', 'CNTT23B', 'CNTT', '2312401022@ct.edu.vn', '0922345678', 'Đà Nẵng', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401023', 'Nguyễn Quốc Anh', '2005-09-15', 'Nam', 'CNTT23B', 'CNTT', '2312401023@ct.edu.vn', '0923456789', 'TP. Hồ Chí Minh', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401024', 'Phạm Thị Hoa', '2005-01-09', 'Nữ', 'CNTT23B', 'CNTT', '2312401024@ct.edu.vn', '0924567890', 'Bình Dương', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401025', 'Lê Văn Thành', '2005-10-11', 'Nam', 'CNTT23B', 'CNTT', '2312401025@ct.edu.vn', '0925678901', 'Hà Nội', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401026', 'Trương Mỹ Linh', '2005-08-21', 'Nữ', 'CNTT23B', 'CNTT', '2312401026@ct.edu.vn', '0926789012', 'Huế', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401027', 'Nguyễn Văn Khánh', '2005-02-03', 'Nam', 'CNTT23B', 'CNTT', '2312401027@ct.edu.vn', '0927890123', 'Cần Thơ', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401028', 'Đặng Thị Ngọc', '2005-06-27', 'Nữ', 'CNTT23B', 'CNTT', '2312401028@ct.edu.vn', '0928901234', 'TP. Hồ Chí Minh', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401029', 'Lê Minh Trí', '2005-04-12', 'Nam', 'CNTT23B', 'CNTT', '2312401029@ct.edu.vn', '0929012345', 'Hà Nội', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401030', 'Trần Thị Phương', '2005-07-07', 'Nữ', 'CNTT23B', 'CNTT', '2312401030@ct.edu.vn', '0930123456', 'Đồng Nai', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401031', 'Nguyễn Minh Tuấn', '2005-09-12', 'Nam', 'CNTT23C', 'CNTT', '2312401031@ct.edu.vn', '0931234567', 'Huế', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401032', 'Trần Thị Hằng', '2005-10-20', 'Nữ', 'CNTT23C', 'CNTT', '2312401032@ct.edu.vn', '0932345678', 'Đà Nẵng', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401033', 'Phạm Tuấn Kiệt', '2005-05-15', 'Nam', 'CNTT23C', 'CNTT', '2312401033@ct.edu.vn', '0933456789', 'TP. Hồ Chí Minh', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401034', 'Lê Quốc Bảo', '2004-11-25', 'Nam', 'CNTT23C', 'CNTT', '2312401034@ct.edu.vn', '0934567890', 'Huế', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312401035', 'Đoàn Mỹ Hạnh', '2005-01-18', 'Nữ', 'CNTT23C', 'CNTT', '2312401035@ct.edu.vn', '0935678901', 'Cần Thơ', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312402001', 'Nguyễn Văn Nam', '2005-09-12', 'Nam', 'CK23A', 'CK', '2312402001@ct.edu.vn', '0936789012', 'Huế', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312402002', 'Trần Hồng Vân', '2005-10-20', 'Nữ', 'CK23B', 'CK', '2312402002@ct.edu.vn', '0937890123', 'Đà Nẵng', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312402003', 'Phạm Tuấn Kiệt', '2005-05-15', 'Nam', 'CK24A', 'CK', '2312402003@ct.edu.vn', '0938901234', 'TP. Hồ Chí Minh', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312402004', 'Lê Quốc Bảo', '2004-11-25', 'Nam', 'CK24B', 'CK', '2312402004@ct.edu.vn', '0939012345', 'Huế', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312403001', 'Trần Hoàng Duy', '2005-02-16', 'Nam', 'DDT23A', 'DDT', '2312403001@ct.edu.vn', '0940123456', 'Đồng Nai', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312403002', 'Nguyễn Minh Tâm', '2005-06-30', 'Nam', 'DDT23B', 'DDT', '2312403002@ct.edu.vn', '0941234567', 'Cần Thơ', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312403003', 'Phan Thị Hòa', '2005-08-12', 'Nữ', 'DDT23B', 'DDT', '2312403003@ct.edu.vn', '0942345678', 'Huế', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312403004', 'Lê Thị My', '2005-01-22', 'Nữ', 'DDT24A', 'DDT', '2312403004@ct.edu.vn', '0943456789', 'Đà Nẵng', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2312403005', 'Nguyễn Văn Lâm', '2005-11-02', 'Nam', 'DDT24B', 'DDT', '2312403005@ct.edu.vn', '0944567890', 'TP. Hồ Chí Minh', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('241000011', 'Mai Trần Trường Thịnh', '2025-11-22', 'Nữ', 'CNTT23A', 'CNTT', '', '0912345345', 'htk', '$2y$10$7h15CIZzgNzusZ/wfAMcOOoDZ8zewm9R6uecaiC6kGrcfjRUYptku', 'Đang học'),
('2412401001', 'Phạm Thanh Hải', '2006-05-18', 'Nam', 'CNTT24A', 'CNTT', '2412401001@ct.edu.vn', '0945678901', 'Huế', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2412401002', 'Lê Ngọc Ánh', '2006-08-21', 'Nữ', 'CNTT24B', 'CNTT', '2412401002@ct.edu.vn', '0946789012', 'Hà Nội', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2412401003', 'Trần Quốc Tuấn', '2006-07-25', 'Nam', 'CNTT24A', 'CNTT', '2412401003@ct.edu.vn', '0947890123', 'Huế', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2412401004', 'Lê Ngọc Trâm', '2006-12-30', 'Nữ', 'CNTT24B', 'CNTT', '2412401004@ct.edu.vn', '0948901234', 'Đà Nẵng', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2412401005', 'Ngô Văn Phú', '2006-04-22', 'Nam', 'CNTT24A', 'CNTT', '2412401005@ct.edu.vn', '0949012345', 'TP. HCM', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2412401006', 'Đinh Thị Thảo', '2006-09-17', 'Nữ', 'CNTT24B', 'CNTT', '2412401006@ct.edu.vn', '0950123456', 'Đồng Nai', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2412401007', 'Võ Thanh Bình', '2006-03-02', 'Nam', 'CNTT24A', 'CNTT', '2412401007@ct.edu.vn', '0951234567', 'Huế', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học'),
('2412401008', 'Nguyễn Mai Hương', '2006-10-10', 'Nữ', 'CNTT24B', 'CNTT', '2412401008@ct.edu.vn', '0952345678', 'Cần Thơ', '$2y$10$TKh8H1.PxfwTtYvEV/0y2O7n4eGcEJ7n2Mn6KZbXAbQW2ShpXQa6K', 'Đang học');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thong_bao`
--

CREATE TABLE `thong_bao` (
  `id` int(11) NOT NULL,
  `tieude` varchar(200) NOT NULL,
  `noidung` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thong_bao`
--

INSERT INTO `thong_bao` (`id`, `tieude`, `noidung`, `created_at`) VALUES
(1, 'Thông báo đăng ký học phần học kỳ II', 'Sinh viên toàn trường đăng ký học phần từ ngày 10/01/2025 đến 14/01/2025.', '2025-01-05 15:25:30'),
(2, 'Thông báo nghỉ Tết Nguyên Đán', 'Toàn trường nghỉ Tết từ 24/01/2025 đến 02/02/2025. Chúc mừng năm mới!', '2025-01-20 07:10:20'),
(3, 'Thông báo lịch học kỳ II', 'Các lớp khóa 23 và 24 bắt đầu học kỳ II từ ngày 15/02/2025. Sinh viên theo dõi thời khóa biểu trên website.', '2025-01-25 10:02:01');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `khoa`
--
ALTER TABLE `khoa`
  ADD PRIMARY KEY (`makhoa`);

--
-- Chỉ mục cho bảng `lop`
--
ALTER TABLE `lop`
  ADD PRIMARY KEY (`malop`),
  ADD KEY `makhoa` (`makhoa`);

--
-- Chỉ mục cho bảng `quan_tri_vien`
--
ALTER TABLE `quan_tri_vien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `sinh_vien`
--
ALTER TABLE `sinh_vien`
  ADD PRIMARY KEY (`mssv`),
  ADD KEY `malop` (`malop`),
  ADD KEY `makhoa` (`makhoa`);

--
-- Chỉ mục cho bảng `thong_bao`
--
ALTER TABLE `thong_bao`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `quan_tri_vien`
--
ALTER TABLE `quan_tri_vien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `thong_bao`
--
ALTER TABLE `thong_bao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `lop`
--
ALTER TABLE `lop`
  ADD CONSTRAINT `lop_ibfk_1` FOREIGN KEY (`makhoa`) REFERENCES `khoa` (`makhoa`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ràng buộc cho bảng `sinh_vien`
--
ALTER TABLE `sinh_vien`
  ADD CONSTRAINT `sinh_vien_ibfk_1` FOREIGN KEY (`malop`) REFERENCES `lop` (`malop`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sinh_vien_ibfk_2` FOREIGN KEY (`makhoa`) REFERENCES `khoa` (`makhoa`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
INSERT INTO quan_tri_vien
(username, password, hoten, email, last_login)
VALUES
(
    'trnghaidong',
    '$2y$10$LlYJxvhmoTHfeqiPWZwvgOJpXCry8rf1a5SGFNFFZ7E/PZfw4QBs6',
    'Trần Nguyễn Hải Đông',
    '0306251170@caothang.edu.vn',
    NULL
);