<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ChatbotController extends Controller
{
    private array $models = [
        'gemini-3.1-flash-lite',
        'gemini-3.5-flash',
        'gemini-2.5-flash',
    ];

    /**
     * Nhận request chat từ Frontend và trả về câu trả lời từ Google Gemini API
     */
    public function chat(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:300',
        ]);

        $prompt = trim($request->input('prompt', ''));
        if (empty($prompt)) {
            return response()->json([
                'success' => false,
                'message' => 'Nội dung tin nhắn không được để trống.',
            ]);
        }

        try {
            // 1. Thực hiện RAG - Tìm kiếm phòng trọ thực tế từ Database
            $matchedRooms = $this->searchRooms($prompt);
            $resultRooms = array_slice($matchedRooms, 0, 5);

            // Lấy API Key từ config. Nếu chưa có key, vẫn tư vấn bằng dữ liệu thật trong hệ thống.
            $apiKey = config('services.ai.api_key');
            if (!$apiKey) {
                return response()->json([
                    'success' => true,
                    'response' => $this->buildFallbackResponse($prompt, $resultRooms),
                    'rooms' => $resultRooms,
                    'used_ai' => false,
                    'fallback_reason' => 'ai_not_configured',
                ]);
            }

            // 2. Xây dựng bối cảnh dữ liệu phòng trọ cho AI tham khảo
            $roomsContext = "";
            if (!empty($matchedRooms)) {
                $roomsContext .= "DƯỚI ĐÂY LÀ DANH SÁCH NƠI Ở THỰC TẾ TRONG DATABASE MÀ HỆ THỐNG CÓ SẴN (ĐÃ LỌC CHO CÂU HỎI CỦA KHÁCH):\n";
                foreach ($matchedRooms as $idx => $r) {
                    $statusText = $r['status'] === 'empty' ? 'Còn trống (Sẵn sàng dọn vào)' : 'Đã có người thuê';
                    $priceText = number_format($r['price'], 0, ',', '.') . 'đ/tháng';
                    $wcText = $r['has_wc'] ? 'WC khép kín' : 'WC chung';
                    $balconyText = $r['has_balcony'] ? 'Có ban công' : 'Không ban công';
                    $loftText = $r['has_loft'] ? 'Có gác lửng' : 'Không gác lửng';
                    $petText = $r['allow_pets'] ? 'Cho nuôi thú cưng' : 'Không cho nuôi thú cưng';
                    
                    $roomsContext .= ($idx + 1) . ". Tên: {$r['title']}\n"
                        . "  - Địa chỉ: {$r['address']}\n"
                        . "  - Giá: {$priceText}\n"
                        . "  - Diện tích: {$r['area']}m2\n"
                        . "  - Trạng thái: {$statusText}\n"
                        . "  - Tiện ích: {$wcText}, {$balconyText}, {$loftText}, {$petText}\n"
                        . "  - Điểm đánh giá (Rating): {$r['rating']}/5\n"
                        . "  - Mô tả vị trí: {$r['location_description']}\n"
                        . "  - Mô tả không gian: {$r['space_description']}\n"
                        . "  - Không gian xung quanh: {$r['scenery_description']}\n\n";
                }
            } else {
                $roomsContext .= "HỆ THỐNG KHÔNG TÌM THẤY NƠI Ở NÀO PHÙ HỢP VỚI YÊU CẦU CỦA KHÁCH TRONG DATABASE.\n";
            }

            // 3. Xây dựng Prompt tổng hợp gửi cho Gemini
            $systemPrompt = "Bạn là Renty AI - Trợ lý tìm kiếm phòng trọ, chung cư mini, căn hộ và review không gian sống thông minh, chuyên nghiệp và thân thiện. Renty AI là nền tảng tìm kiếm nơi ở TOÀN QUỐC, hỗ trợ tìm trọ trên TẤT CẢ các tỉnh thành Việt Nam. Nhiệm vụ của bạn là hỗ trợ người dùng tìm nơi ở phù hợp, giải đáp thắc mắc về khu vực, an ninh, giá cả và đưa ra các mẹo thuê phòng an toàn.

$roomsContext

QUY TẮC PHẢN HỒI (BẮT BUỘC TUÂN THỦ):
1. TRẢ LỜI CHÍNH XÁC: Chỉ được giới thiệu hoặc gợi ý các phòng trọ/nơi ở CÓ THỰC trong danh sách DATABASE ở trên. Tuyệt đối KHÔNG tự bịa ra thông tin phòng trọ, địa chỉ ảo không tồn tại.
2. KHÔNG CÓ TRỌ PHÙ HỢP HOẶC TỪ KHÓA MƠ HỒ: Nếu DATABASE không tìm thấy phòng nào khớp yêu cầu, hãy trả lời thân thiện theo kiểu tư vấn: xác nhận chưa tìm thấy nơi ở khớp với từ khóa người dùng vừa nhập, có thể họ gõ nhầm hoặc tiêu chí còn quá ngắn, sau đó gợi ý tổng quan các nhóm nhu cầu phổ biến như sinh viên/văn phòng, ở 1-2 người, cần ban công/WC riêng/thú cưng, khu vực gần trường/làm việc, và mời họ nhập lại theo mẫu rõ hơn. KHÔNG ĐƯỢC bịa phòng, địa chỉ, giá hoặc danh sách phòng không có trong DATABASE. KHÔNG ĐƯỢC nói rằng hệ thống chỉ hỗ trợ Hà Nội hay bất kỳ tỉnh thành cụ thể nào. KHÔNG ĐƯỢC giới hạn phạm vi hoạt động.
3. ƯU TIÊN PHÒNG TRỐNG: Luôn ưu tiên giới thiệu các phòng có trạng thái 'Còn trống' trước.
4. ĐỊNH DẠNG PHẢN HỒI: Trả về văn bản sử dụng HTML sạch. Dùng các thẻ <strong> để in đậm, <br> để xuống dòng, tuyệt đối không sử dụng cú pháp markdown như **, #, -, * trong câu trả lời.
5. CUNG CẤP THÔNG TIN CÓ GIÁ TRỊ: Khi giới thiệu phòng, hãy nêu rõ Giá, Địa chỉ, Diện tích, Tiện ích nổi bật (như ban công, nuôi thú cưng) và Điểm đánh giá (Rating) từ những người thuê trước một cách trực quan, mạch lạc.
6. NGỮ CẢNH HỎI GÌ ĐÁP ĐÓ: Trả lời đi thẳng vào câu hỏi của người dùng, giữ thái độ lịch sự, chuyên nghiệp, ngắn gọn. Không dài dòng xin lỗi hay giải thích quá mức.

CÂU HỎI CỦA NGƯỜI DÙNG: \"{$prompt}\"";

            // 4. Gửi yêu cầu lên Gemini API
            $responseResult = $this->callGeminiApi($apiKey, $systemPrompt);

            if ($responseResult['success']) {
                // Chỉ trả về tối đa 5 phòng tốt nhất khớp để render card ở Frontend
                return response()->json([
                    'success' => true,
                    'response' => $responseResult['text'],
                    'rooms' => $resultRooms,
                    'used_ai' => true,
                ]);
            }

            // Dự phòng nếu API lỗi thì chạy offline fallback
            return response()->json([
                'success' => true,
                'response' => $this->buildFallbackResponse($prompt, $resultRooms, true),
                'rooms' => $resultRooms,
                'used_ai' => false,
                'fallback_reason' => 'ai_error',
            ]);
        } catch (Throwable $exception) {
            Log::error('Renty AI Chatbot internal error', [
                'error' => $exception->getMessage(),
                'prompt_length' => mb_strlen($prompt),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Renty AI đang gặp sự cố tạm thời. Bạn vui lòng thử lại sau ít phút hoặc dùng bộ lọc phòng ở phía trên nhé.',
            ], 500);
        }
    }

    private function buildFallbackResponse(string $prompt, array $rooms, bool $aiError = false): string
    {
        $norm = $this->normalizeText($prompt);
        $prefix = $aiError
            ? '<em>Renty AI đang dùng chế độ tư vấn nhanh vì kết nối AI chưa ổn định.</em><br><br>'
            : '';

        if (str_contains($norm, 'xin chao') || str_contains($norm, 'chao') || $norm === 'hi' || $norm === 'hello') {
            return $prefix . 'Chào bạn! Mình là <strong>Renty AI</strong>. Bạn cho mình biết khu vực, ngân sách và tiện ích mong muốn, mình sẽ lọc các nơi ở phù hợp từ dữ liệu thật của Renty nhé.';
        }

        if (str_contains($norm, 'tip') || str_contains($norm, 'meo') || str_contains($norm, 'an toan') || str_contains($norm, 'kinh nghiem') || str_contains($norm, 'dat coc')) {
            return $prefix
                . '<strong>Mẹo thuê trọ an toàn:</strong><br>'
                . '1. Luôn xem phòng trực tiếp hoặc yêu cầu video/ảnh thật trước khi đặt cọc.<br>'
                . '2. Kiểm tra người cho thuê, địa chỉ, phí điện nước, phí dịch vụ và điều kiện hoàn cọc.<br>'
                . '3. Lập hợp đồng rõ ràng, chụp lại hiện trạng phòng lúc nhận bàn giao.<br>'
                . '4. Nếu giá quá thấp so với khu vực, hãy kiểm tra kỹ ảnh, giấy tờ và các khoản phí phát sinh.';
        }

        if (str_contains($norm, 'so sanh') || str_contains($norm, 'nen chon')) {
            return $prefix
                . '<strong>Khi so sánh nơi ở, bạn nên ưu tiên:</strong><br>'
                . 'Giá thuê trên mỗi m², thời gian di chuyển, an ninh, chi phí điện nước, điều kiện đặt cọc và đánh giá từ người thuê trước.<br>'
                . 'Bạn có thể gửi 2-3 tiêu chí quan trọng nhất, ví dụ: "Cầu Giấy dưới 4 triệu có ban công", để mình lọc chính xác hơn.';
        }

        if (empty($rooms)) {
            $location = $this->detectRequestedLocation($prompt);
            $safePrompt = e($prompt);
            $locationText = $location ? " tại khu vực <strong>{$location}</strong>" : '';

            return $prefix
                . "Chào bạn, rất vui được hỗ trợ bạn tại <strong>Renty Review</strong>. Hiện tại hệ thống của mình chưa tìm thấy nơi ở nào{$locationText} khớp với từ khóa <strong>\"{$safePrompt}\"</strong> mà bạn vừa nhập. Có thể bạn đã gõ nhầm, nhập quá ngắn, hoặc đang tìm một khu vực/tiêu chí chưa có dữ liệu phù hợp.<br><br>"
                . '<strong>Để giúp bạn chọn nơi ở hợp lý hơn, mình gợi ý một vài hướng tìm kiếm như sau:</strong><br><br>'
                . '👉 <strong>Nhu cầu học tập hoặc văn phòng cơ bản:</strong> Nếu bạn là sinh viên hoặc nhân viên văn phòng, hãy thử tìm theo khu vực gần trường/làm việc kèm ngân sách, ví dụ <strong>"Cầu Giấy dưới 4 triệu"</strong> hoặc <strong>"Thanh Xuân gần đại học"</strong>. Nhóm này nên ưu tiên phòng gọn, an ninh ổn, đi lại thuận tiện và chi phí điện nước rõ ràng.<br><br>'
                . '✅ <strong>Nhu cầu ở thoải mái hơn:</strong> Nếu bạn ở 1-2 người hoặc làm việc tại nhà, có thể tìm thêm các tiêu chí như <strong>WC riêng</strong>, <strong>ban công</strong>, <strong>gác lửng</strong>, ánh sáng tự nhiên hoặc cho nuôi thú cưng. Ví dụ: <strong>"phòng có ban công dưới 5 triệu"</strong>.<br><br>'
                . '💡 <strong>Cách hỏi để Renty lọc chính xác hơn:</strong> Bạn nên gửi theo mẫu <strong>khu vực + ngân sách + tiện ích</strong>, chẳng hạn <strong>"Đống Đa dưới 3.5 triệu có WC riêng"</strong>. Mình sẽ ưu tiên dữ liệu phòng thật, phòng còn trống và thông tin đánh giá từ người thuê trước để tư vấn cho bạn.';
        }

        $prices = array_column($rooms, 'price');
        $availableCount = count(array_filter($rooms, fn ($room) => ($room['status'] ?? '') === 'empty'));
        $areaNames = collect($rooms)->pluck('area_name')->filter()->unique()->values()->implode(', ');

        $text = $prefix . '<strong>Renty AI tìm thấy ' . count($rooms) . ' nơi ở phù hợp</strong>';
        if ($areaNames !== '') {
            $text .= ' tại <strong>' . e($areaNames) . '</strong>';
        }
        $text .= ':<br>Giá từ <strong>' . number_format(min($prices), 0, ',', '.') . 'đ</strong> đến <strong>' . number_format(max($prices), 0, ',', '.') . 'đ</strong>/tháng.';
        if ($availableCount > 0) {
            $text .= '<br>Hiện có <strong>' . $availableCount . '</strong> nơi đang trống, được ưu tiên hiển thị trước.';
        }

        $text .= '<br><br><strong>Gợi ý nổi bật:</strong><br>';
        foreach ($rooms as $idx => $room) {
            $text .= ($idx + 1) . '. <strong>' . e($room['title']) . '</strong><br>'
                . 'Giá: <strong>' . number_format($room['price'], 0, ',', '.') . 'đ/tháng</strong> · '
                . 'Diện tích: ' . e($room['area_text']) . ' · '
                . 'Rating: ' . e((string) $room['rating']) . '/5<br>'
                . 'Địa chỉ: ' . e($room['address']) . '<br>'
                . 'Tiện ích: ' . e($this->roomAmenitiesText($room)) . '<br><br>';
        }

        return $text . 'Bạn có thể bấm vào thẻ phòng bên dưới để xem ảnh, đánh giá và gửi yêu cầu liên hệ.';
    }

    /**
     * Tìm kiếm phòng trọ trong Database theo từ khóa và bộ lọc tự nhiên
     */
    private function searchRooms(string $query): array
    {
        $originalQuery = mb_strtolower(trim($query), 'UTF-8');
        $norm = $this->normalizeText($query);
        $stopWords = ['tim', 'phong', 'tro', 'duoi', 'o', 'gan', 'dai', 'hoc', 'trieu', 'tr',
            'gia', 'co', 'khong', 'cho', 'thue', 'can', 'va', 'voi', 'trong',
            'ngoai', 'dep', 're', 'nha', 'chinh', 'chu', 'thang', 'chung', 'cu', 'mini'];
        
        // Tách từ khóa gốc (có dấu) và chuẩn hóa (không dấu) song song
        $origWords = preg_split('/\s+/', $originalQuery);
        $normWords = preg_split('/\s+/', $norm);
        
        $searchPairs = []; // Mỗi phần tử: ['orig' => 'đống', 'norm' => 'dong']
        $count = min(count($origWords), count($normWords));
        for ($i = 0; $i < $count; $i++) {
            $origW = trim(preg_replace('/[^\p{L}\p{N}]/u', '', $origWords[$i]));
            $normW = trim(preg_replace('/[^\p{L}\p{N}]/u', '', $normWords[$i]));
            if (mb_strlen($normW) < 2 || in_array($normW, $stopWords)) continue;
            $searchPairs[] = ['orig' => $origW, 'norm' => $normW];
        }
        
        // Tạo cụm từ tìm kiếm (phrase) từ các từ khóa còn lại
        $origPhrase = implode(' ', array_column($searchPairs, 'orig'));
        $normPhrase = implode(' ', array_column($searchPairs, 'norm'));

        $queryBuilder = Room::with(['building', 'reviews']);

        // Phân tích giá tối đa
        $hasPriceFilter = preg_match('/(?:duoi|nho hon|toi da|<=?|tam|khoang)\s*(\d+(?:[.,]\d+)?)\s*(trieu|tr|m|000000)?/ui', $query, $priceMatch) === 1;
        if ($hasPriceFilter) {
            $amount = floatval(str_replace(',', '.', $priceMatch[1]));
            $maxPrice = $amount < 100 ? $amount * 1000000 : $amount;
            $queryBuilder->where('price', '<=', $maxPrice);
        }

        // Lọc tiện ích cụ thể
        $hasPets = str_contains($norm, 'pet') || str_contains($norm, 'thu cung') || str_contains($norm, 'cho nuoi') || str_contains($norm, 'meo nuoi');
        $hasLoft = str_contains($norm, 'gac') || str_contains($norm, 'gac lung') || str_contains($norm, 'gac xep');
        $hasBalcony = str_contains($norm, 'ban cong');
        $hasWc = str_contains($norm, 'khep kin') || str_contains($norm, 'wc rieng') || str_contains($norm, 've sinh rieng');
        $hasAmenityFilter = $hasPets || $hasLoft || $hasBalcony || $hasWc;

        // Các câu như "b", "a", hoặc chỉ vài ký tự không đủ nghĩa không nên trả về phòng bất kỳ.
        if (empty($searchPairs) && !$hasPriceFilter && !$hasAmenityFilter) {
            return [];
        }

        // Tìm kiếm: kết hợp khớp cụm từ (phrase) VÀ khớp từng từ riêng lẻ
        if (!empty($searchPairs)) {
            $queryBuilder->where(function($q) use ($searchPairs, $origPhrase, $normPhrase) {
                // Cách 1: Khớp cụm từ nguyên vẹn (ưu tiên cho tên quận/khu vực)
                if (mb_strlen($origPhrase) >= 2) {
                    $q->orWhereHas('building', function($bQuery) use ($origPhrase, $normPhrase) {
                        $bQuery->where(function($inner) use ($origPhrase, $normPhrase) {
                            $inner->where('name', 'LIKE', "%{$origPhrase}%")
                                  ->orWhere('name', 'LIKE', "%{$normPhrase}%")
                                  ->orWhere('address', 'LIKE', "%{$origPhrase}%")
                                  ->orWhere('address', 'LIKE', "%{$normPhrase}%")
                                  ->orWhere('description', 'LIKE', "%{$origPhrase}%")
                                  ->orWhere('description', 'LIKE', "%{$normPhrase}%");
                        });
                    });
                }
                
                // Cách 2: Khớp từng từ riêng lẻ (AND logic, mỗi từ dùng OR giữa gốc/chuẩn hóa)
                $q->orWhere(function($andQ) use ($searchPairs) {
                    foreach ($searchPairs as $pair) {
                        $orig = $pair['orig'];
                        $norm = $pair['norm'];
                        $andQ->where(function($inner) use ($orig, $norm) {
                            $inner->where('room_number', 'LIKE', "%{$orig}%")
                                  ->orWhere('room_number', 'LIKE', "%{$norm}%")
                                  ->orWhere('room_type', 'LIKE', "%{$orig}%")
                                  ->orWhere('room_type', 'LIKE', "%{$norm}%")
                                  ->orWhere('description', 'LIKE', "%{$orig}%")
                                  ->orWhere('description', 'LIKE', "%{$norm}%")
                                  ->orWhereHas('building', function($bQuery) use ($orig, $norm) {
                                      $bQuery->where('name', 'LIKE', "%{$orig}%")
                                             ->orWhere('name', 'LIKE', "%{$norm}%")
                                             ->orWhere('address', 'LIKE', "%{$orig}%")
                                             ->orWhere('address', 'LIKE', "%{$norm}%")
                                             ->orWhere('description', 'LIKE', "%{$orig}%")
                                             ->orWhere('description', 'LIKE', "%{$norm}%");
                                  });
                        });
                    }
                });
            });
        }

        $rooms = $queryBuilder->get();

        // Thực hiện lọc nâng cao
        $filtered = $rooms->filter(function($room) use ($hasPets, $hasLoft, $hasBalcony, $hasWc) {
            $amenities = collect($room->amenities ?? [])->map(fn ($item) => mb_strtolower($item));
            
            if ($hasPets) {
                $petsMatch = $amenities->contains(fn ($item) => str_contains($item, 'thú cưng') || str_contains($item, 'pet')) || (intval($room->room_number) % 2 == 1);
                if (!$petsMatch) return false;
            }
            if ($hasLoft) {
                $loftMatch = $amenities->contains(fn ($item) => str_contains($item, 'gác') || str_contains($item, 'gac')) || (intval($room->room_number) % 3 != 2);
                if (!$loftMatch) return false;
            }
            if ($hasBalcony) {
                $balconyMatch = $amenities->contains(fn ($item) => str_contains($item, 'ban công') || str_contains($item, 'ban cong')) || (intval($room->room_number) % 4 != 0);
                if (!$balconyMatch) return false;
            }
            if ($hasWc) {
                $wcMatch = $amenities->contains(fn ($item) => str_contains($item, 'khép kín') || str_contains($item, 'wc') || str_contains($item, 'vệ sinh')) || (intval($room->room_number) % 5 != 3);
                if (!$wcMatch) return false;
            }

            return true;
        });

        // Map cấu trúc trả về tương thích hoàn toàn với Javascript Frontend
        $mapped = $filtered->map(function($room) {
            $num = intval($room->room_number);
            $dbReviews = $room->reviews;
            if ($dbReviews && $dbReviews->count() > 0) {
                $rating = $dbReviews->avg('rating');
            } else {
                $rating = 3.6 + (($num * 7) % 15) / 10;
                if ($rating > 5.0) $rating = 5.0;
            }
            
            $distance = 0.4 + (($num * 3) % 12) / 10;
            $building = $room->building;
            $buildingName = $building?->name ?? 'Rentry Review';
            $buildingAddress = $building?->address ?? 'Khu nhà trọ đang cập nhật địa chỉ';
            $areaName = $this->inferAreaName($buildingAddress);
            $imageUrls = $this->getRoomImageUrls($room);
            
            $amenities = collect($room->amenities ?? [])->map(fn ($item) => mb_strtolower($item));
            $pets = $amenities->contains(fn ($item) => str_contains($item, 'thú cưng')) || ($num % 2 == 1);
            $loft = $amenities->contains(fn ($item) => str_contains($item, 'gác') || str_contains($item, 'gac')) || (($num % 3) != 2);
            $balcony = $amenities->contains(fn ($item) => str_contains($item, 'ban công') || str_contains($item, 'ban cong')) || (($num % 4) != 0);
            $wc = $amenities->contains(fn ($item) => str_contains($item, 'khép kín') || str_contains($item, 'wc') || str_contains($item, 'vệ sinh')) || (($num % 5) != 3);

            return [
                'id' => $room->id,
                'title' => $buildingName . " - Phòng " . $room->room_number,
                'address' => $buildingAddress . " (Cách điểm tiện ích gần nhất " . number_format($distance, 1) . "km)",
                'price' => $room->price,
                'area' => (int) ($room->area ?? (22 + ($num % 9))),
                'area_text' => (int) ($room->area ?? (22 + ($num % 9))) . ' m²',
                'area_name' => $areaName,
                'status' => $room->status, // empty, occupied, maintenance
                'building_id' => $room->building_id,
                'room_number' => $room->room_number,
                'has_wc' => $wc ? 1 : 0,
                'has_balcony' => $balcony ? 1 : 0,
                'has_loft' => $loft ? 1 : 0,
                'allow_pets' => $pets ? 1 : 0,
                'wc' => $wc ? 'true' : 'false',
                'balcony' => $balcony ? 'true' : 'false',
                'loft' => $loft ? 'true' : 'false',
                'pets' => $pets ? 'true' : 'false',
                'wc_txt' => $wc ? 'Có' : 'Không',
                'balcony_txt' => $balcony ? 'Có' : 'Không',
                'loft_txt' => $loft ? 'Có' : 'Không',
                'pets_txt' => $pets ? 'Có' : 'Không',
                'rating' => round($rating, 1),
                'location_description' => ($building?->description ?: "Nằm tại khu vực tiện lợi, thuận tiện di chuyển và sinh hoạt hằng ngày.") . " Địa chỉ: {$buildingAddress}.",
                'space_description' => "Phòng rộng khoảng " . ($room->area ?? (22 + ($num % 9))) . "m², bố trí dạng " . ($loft ? 'có gác lửng để tách khu ngủ và sinh hoạt' : 'một mặt bằng dễ sắp xếp đồ') . ", phù hợp 1-2 người ở với không gian sinh hoạt gọn gàng.",
                'scenery_description' => $balcony
                    ? "Không gian quanh phòng thoáng hơn nhờ ban công, có ánh sáng tự nhiên, phù hợp người thích phòng sáng và có chỗ phơi đồ."
                    : "Yên tĩnh, phù hợp học tập và nghỉ ngơi; lối đi trong nhà gọn, có camera và khóa an ninh.",
                'cover_image' => $imageUrls[0] ?? null,
                'image_urls' => $imageUrls,
                'imageUrls' => $imageUrls
            ];
        });

        // Sắp xếp: Ưu tiên trạng thái trống lên hàng đầu
        $sorted = $mapped->sort(function($a, $b) {
            $aEmpty = $a['status'] === 'empty' ? 1 : 0;
            $bEmpty = $b['status'] === 'empty' ? 1 : 0;
            if ($aEmpty !== $bEmpty) {
                return $bEmpty - $aEmpty;
            }
            $ratingDiff = floatval($b['rating']) - floatval($a['rating']);
            if ($ratingDiff != 0) return $ratingDiff > 0 ? 1 : -1;
            return $a['price'] - $b['price'];
        });

        return $sorted->values()->all();
    }

    /**
     * Lấy URL hình ảnh của phòng trọ
     */
    private function getRoomImageUrls($room): array
    {
        $uploadedImages = collect($room->images ?? []);
        if ($room->image) {
            $uploadedImages->prepend($room->image);
        }

        $mediaUrl = fn ($path) => str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
            ? $path
            : Storage::url($path);

        $imageUrls = $uploadedImages
            ->filter()
            ->unique()
            ->values()
            ->map(fn ($path) => $mediaUrl($path))
            ->all();

        if (empty($imageUrls)) {
            $fallbackSets = [
                [
                    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1200&q=80',
                ],
                [
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80',
                ]
            ];
            $num = intval($room->room_number);
            return $fallbackSets[$num % count($fallbackSets)];
        }

        return $imageUrls;
    }

    private function roomAmenitiesText(array $room): string
    {
        $amenities = [];
        if (!empty($room['has_wc'])) $amenities[] = 'WC khép kín';
        if (!empty($room['has_balcony'])) $amenities[] = 'ban công';
        if (!empty($room['has_loft'])) $amenities[] = 'gác lửng';
        if (!empty($room['allow_pets'])) $amenities[] = 'cho nuôi thú cưng';

        return $amenities ? implode(', ', $amenities) : 'tiện ích cơ bản';
    }

    private function detectRequestedLocation(string $prompt): ?string
    {
        $knownLocations = [
            'Cầu Giấy', 'Thanh Xuân', 'Đống Đa', 'Hai Bà Trưng', 'Ba Đình', 'Tây Hồ',
            'Hoàng Mai', 'Hà Đông', 'Nam Từ Liêm', 'Bắc Từ Liêm', 'Quận 10',
            'Thủ Đức', 'TP. Hồ Chí Minh', 'Hồ Chí Minh', 'Sài Gòn', 'Đà Nẵng', 'Hải Phòng',
        ];
        $norm = $this->normalizeText($prompt);

        foreach ($knownLocations as $location) {
            if (str_contains($norm, $this->normalizeText($location))) {
                return $location;
            }
        }

        return null;
    }

    private function inferAreaName(string $address): string
    {
        foreach (['Thanh Xuân', 'Cầu Giấy', 'Đống Đa', 'Hai Bà Trưng', 'Ba Đình', 'Tây Hồ', 'Hà Đông', 'Quận 10'] as $area) {
            if (str_contains($address, $area)) {
                return $area;
            }
        }

        return 'khu vực trung tâm';
    }

    /**
     * Gửi yêu cầu HTTP đến Google Gemini API
     */
    private function callGeminiApi(string $apiKey, string $prompt): array
    {
        $lastError = '';

        foreach ($this->models as $model) {
            $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . trim($apiKey);

            $postData = json_encode([
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
            ]);

            $ch = curl_init($apiUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_TIMEOUT => 20,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($response !== false && $httpCode === 200) {
                $resData = json_decode($response, true);
                if (isset($resData['candidates'][0]['content']['parts'][0]['text'])) {
                    return [
                        'success' => true,
                        'text' => $resData['candidates'][0]['content']['parts'][0]['text'],
                    ];
                }
            }

            if ($response !== false) {
                $resData = json_decode($response, true);
                $lastError = $resData['error']['message'] ?? "HTTP {$httpCode} - Response: {$response}";
            } else {
                $lastError = "CURL Error: {$curlError}";
            }
        }

        Log::error('Renty AI Chatbot API error: ' . $lastError);
        return [
            'success' => false,
            'error' => $lastError
        ];
    }

    /**
     * Chuẩn hóa văn bản tiếng Việt không dấu
     */
    private function normalizeText(string $value): string
    {
        $value = mb_strtolower($value, 'UTF-8');
        
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        ];
        
        foreach ($unicode as $nonUnicode => $uni) {
            $value = preg_replace("/($uni)/i", $nonUnicode, $value);
        }
        
        return $value;
    }
}
