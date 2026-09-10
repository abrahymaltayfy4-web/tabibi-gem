import 'package:flutter/material.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/widgets/glass_card.dart';

class DoctorDiscoveryScreen extends StatefulWidget {
  const DoctorDiscoveryScreen({super.key});

  @override
  State<DoctorDiscoveryScreen> createState() => _DoctorDiscoveryScreenState();
}

class _DoctorDiscoveryScreenState extends State<DoctorDiscoveryScreen> {
  final _searchController = TextEditingController();

  final List<Map<String, dynamic>> _mockDoctors = [
    {
      'id': 1,
      'name': 'د. خالد العمري',
      'specialty': 'الطب العام والأسرة',
      'price_yer': 6000.0,
      'rating': 4.9,
      'reviews': 24,
      'experience': 8,
    },
    {
      'id': 2,
      'name': 'د. أنس الهلالي',
      'specialty': 'أمراض القلب والأوعية الدموية',
      'price_yer': 8000.0,
      'rating': 5.0,
      'reviews': 42,
      'experience': 12,
    },
    {
      'id': 3,
      'name': 'د. سارة الأحمدي',
      'specialty': 'طب الأطفال وحنيثي الولادة',
      'price_yer': 5000.0,
      'rating': 4.8,
      'reviews': 19,
      'experience': 6,
    },
  ];

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: AppBar(
        title: const Text('دليل الأطباء المعتمدين'),
        backgroundColor: Colors.transparent,
        elevation: 0,
      ),
      body: Padding(
        padding: const EdgeInsets.all(20.0),
        child: Column(
          children: [
            // Search Input
            TextField(
              controller: _searchController,
              decoration: InputDecoration(
                hintText: 'ابحث باسم الطبيب أو التخصص...',
                prefixIcon: const Icon(Icons.search_rounded),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                ),
                filled: true,
                fillColor: isDark ? AppColors.darkSurface : AppColors.lightSurface,
              ),
            ),
            const SizedBox(height: 20),

            // Doctors List
            Expanded(
              child: ListView.separated(
                itemCount: _mockDoctors.length,
                separatorBuilder: (_, __) => const SizedBox(height: 16),
                itemBuilder: (context, index) {
                  final doc = _mockDoctors[index];
                  return GlassCard(
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        CircleAvatar(
                          radius: 30,
                          backgroundColor: isDark ? AppColors.darkSoftBlue : AppColors.lightSoftBlue,
                          child: Icon(
                            Icons.person_pin_rounded,
                            size: 36,
                            color: isDark ? AppColors.darkBackground : AppColors.lightPrimary,
                          ),
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                doc['name'],
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                  color: isDark ? AppColors.darkTextPrimary : AppColors.lightTextPrimary,
                                ),
                              ),
                              const SizedBox(height: 4),
                              Text(
                                doc['specialty'],
                                style: TextStyle(
                                  fontSize: 13,
                                  color: isDark ? AppColors.darkTextSecondary : AppColors.lightTextSecondary,
                                ),
                              ),
                              const SizedBox(height: 8),
                              Row(
                                children: [
                                  const Icon(Icons.star_rounded, color: Colors.amber, size: 18),
                                  const SizedBox(width: 4),
                                  Text(
                                    '${doc['rating']} (${doc['reviews']} تقييم)',
                                    style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                                  ),
                                  const Spacer(),
                                  Text(
                                    '${doc['price_yer']} ر.ي',
                                    style: TextStyle(
                                      fontSize: 15,
                                      fontWeight: FontWeight.bold,
                                      color: isDark ? AppColors.darkLightBlueGlow : AppColors.lightPrimary,
                                    ),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 12),
                              SizedBox(
                                width: double.infinity,
                                child: ElevatedButton(
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(content: Text('الانتقال لحجز موعد مع ${doc['name']}')),
                                    );
                                  },
                                  style: ElevatedButton.styleFrom(
                                    padding: const EdgeInsets.symmetric(vertical: 8),
                                  ),
                                  child: const Text('حجز استشارة رقمية'),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}
